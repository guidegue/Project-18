<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('admin');

$error = ''; // Variable pour les messages d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = nettoyer($_POST['nom']);
    $prenom = nettoyer($_POST['prenom']);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirme_password = $_POST['confirme_password'];
    
    // Validation du mot de passe
    if (empty($mot_de_passe)) {
        $error = "Le mot de passe est obligatoire.";
    } elseif ($mot_de_passe !== $confirme_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($mot_de_passe) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?,?,?,?,?)");
            $stmt->execute([$nom, $prenom, $email, $hash, $role]);
            header('Location: utilisateurs.php');
            exit;
        }
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id=? AND role!='admin'");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: utilisateurs.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = NOT actif WHERE id=? AND role!='admin'");
    $stmt->execute([(int)$_GET['toggle']]);
    header('Location: utilisateurs.php');
    exit;
}

$users = $pdo->query("SELECT * FROM utilisateurs ORDER BY date_inscription DESC")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.dashboard-header{margin-bottom:2rem}
.dashboard-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.two-columns{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:2rem}
.form-card{background:white;border-radius:1rem;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.form-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem 1.5rem}
.form-body{padding:1.5rem}
.form-group{margin-bottom:1rem}
.form-group label{display:block;margin-bottom:0.5rem;font-weight:600;color:#4a5568}
.form-group input,.form-group select{width:100%;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem}
.btn-primary{background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:0.5rem 1rem;border-radius:0.5rem;cursor:pointer}
.btn-full{width:100%}
.error-message{background:#fed7d7;color:#9b2c2c;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.table-container{background:transparent;border-radius:1rem;overflow-x:auto;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:0.75rem;text-align:left}
.data-table td{padding:0.75rem;border-bottom:1px solid #e2e8f0}
.badge{display:inline-block;padding:0.25rem 0.5rem;border-radius:0.25rem;font-size:0.75rem}
.badge-success{background:#c6f6d5;color:#22543d}
.badge-danger{background:#fed7d7;color:#9b2c2c}
.role-admin{background:#fefcbf;color:#975a16}
.role-conseiller{background:#bee3f8;color:#2c5282}
.role-etudiant{background:#c6f6d5;color:#22543d}
</style>

<div class="dashboard-header">
    <h1>👥 Gestion des utilisateurs</h1>
</div>

<div class="two-columns">
    <div class="form-card">
        <div class="form-header"><h3>➕ Ajouter un utilisateur</h3></div>
        <div class="form-body">
            <?php if ($error): ?>
                <div class="error-message">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom" required>
                    </div>
                    <div class="form-group">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label>Mot de passe *</label>
                        <input type="password" name="mot_de_passe" required minlength="6">
                        <small style="color:#6b7280;font-size:0.75rem">Minimum 6 caractères</small>
                    </div>
                    <div class="form-group">
                        <label>Confirmer le mot de passe *</label>
                        <input type="password" name="confirme_password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Rôle *</label>
                    <select name="role">
                        <option value="etudiant">Étudiant</option>
                        <option value="conseiller">Conseiller</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary btn-full">➕ Ajouter l'utilisateur</button>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge role-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                    <td><span class="badge <?= $u['actif'] ? 'badge-success' : 'badge-danger' ?>"><?= $u['actif'] ? 'Actif' : 'Inactif' ?></span></td>
                    <td>
                        <a href="?toggle=<?= $u['id'] ?>" style="margin-right:5px" title="<?= $u['actif'] ? 'Désactiver' : 'Activer' ?>"><?= $u['actif'] ? '🔒' : '🔓' ?></a>
                        <?php if($u['role']!='admin'): ?>
                        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Supprimer définitivement cet utilisateur ?')" style="color:#e53e3e" title="Supprimer">🗑️</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>