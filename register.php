<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (estConnecte()) { redirigerSelonRole(); exit; }

$erreur = '';
$success = false;
$nom = $prenom = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $mdp_confirm = $_POST['mdp_confirm'] ?? '';
    
    if (empty($nom) || empty($prenom) || empty($email)) {
        $erreur = "Tous les champs sont requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide";
    } elseif (strlen($mot_de_passe) < 6) {
        $erreur = "Mot de passe (min 6 caractères)";
    } elseif ($mot_de_passe !== $mdp_confirm) {
        $erreur = "Mots de passe différents";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Email déjà utilisé";
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'etudiant')");
            if ($stmt->execute([$nom, $prenom, $email, $hash])) {
                $user_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO profils_etudiants (etudiant_id) VALUES (?)");
                $stmt->execute([$user_id]);
                $success = true;
            } else {
                $erreur = "Erreur lors de l'inscription";
            }
        }
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<!-- Styles légers pour le formulaire - sans écraser le header/footer -->
<style>
.register-container { max-width: 480px; margin: 1rem auto; }
.register-card { background: transparent; border-radius: 2rem; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
.register-header { background: linear-gradient(135deg, #667eea, #764ba2); padding: 0.8rem; text-align: center; }
.register-header h2 { color: white; font-size: 1.1rem; margin: 0; }
.register-body { padding: 0.8rem 1rem; }
.form-group-compact { margin-bottom: 0.5rem; }
.form-group-compact label { display: block; margin-bottom: 0.2rem; font-weight: 600; font-size: 0.7rem; color: #374151; }
.form-group-compact input { width: 100%; padding: 0.4rem 0.6rem; border: 1.5px solid #0b0557; border-radius: 2rem; font-size: 0.8rem; }
.form-group-compact input:focus { outline: none; border-color: #667eea; }
.form-row-compact { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
.btn-compact { padding: 0.5rem; font-size: 0.8rem; }
.alert-compact { padding: 0.5rem; font-size: 0.75rem; margin-bottom: 0.8rem; border-radius: 0.5rem; }
.form-footer-compact { margin-top: 0.8rem; text-align: center; font-size: 0.7rem; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h2>📝 Créer mon compte</h2>
        </div>
        <div class="register-body">
            <?php if ($success): ?>
                <div class="alert-success alert-compact">✅ Inscription réussie ! <a href="login.php">Se connecter</a></div>
            <?php else: ?>
                <?php if ($erreur): ?>
                    <div class="alert-error alert-compact">❌ <?= $erreur ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-row-compact">
                        <div class="form-group-compact">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= htmlspecialchars($nom) ?>" required>
                        </div>
                        <div class="form-group-compact">
                            <label>Prénom</label>
                            <input type="text" name="prenom" value="<?= htmlspecialchars($prenom) ?>" required>
                        </div>
                    </div>
                    <div class="form-group-compact">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="form-row-compact">
                        <div class="form-group-compact">
                            <label>Mot de passe (min 6)</label>
                            <input type="password" name="mot_de_passe" required>
                        </div>
                        <div class="form-group-compact">
                            <label>Confirmer</label>
                            <input type="password" name="mdp_confirm" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-compact">🚀 S'inscrire</button>
                    <div class="form-footer-compact">
                        <a href="login.php">Déjà inscrit ?</a> | <a href="<?= BASE_URL ?>">Accueil</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>