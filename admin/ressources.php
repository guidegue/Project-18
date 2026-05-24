<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('admin');

$message = '';
$erreur = '';

// Ajouter une ressource
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $titre = nettoyer($_POST['titre']);
    $description = nettoyer($_POST['description']);
    $lien = $_POST['lien'] ?? '';
    $type = $_POST['type'];
    $filiere_id = $_POST['filiere_id'] ? (int)$_POST['filiere_id'] : null;
    
    if (empty($titre)) {
        $erreur = "Le titre est requis.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ressources (titre, description, lien, type, filiere_id, ajoutee_par) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$titre, $description, $lien, $type, $filiere_id, $_SESSION['user_id']]);
        $message = "Ressource ajoutée avec succès.";
        header("refresh:2;url=ressources.php");
    }
}

// Supprimer une ressource
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM ressources WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ressources.php');
    exit;
}

// Récupérer toutes les ressources avec le nom de la filière
$ressources = $pdo->query("
    SELECT r.*, f.nom as filiere_nom
    FROM ressources r
    LEFT JOIN filieres f ON r.filiere_id = f.id
    ORDER BY r.date_ajout DESC
")->fetchAll();

// Récupérer toutes les filières pour le select
$filieres = $pdo->query("SELECT id, nom FROM filieres ORDER BY nom")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>📚 Gestion des ressources pédagogiques</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="alert alert-error"><?= $erreur ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:2rem;">
    <!-- Formulaire d'ajout -->
    <div class="card">
        <h3>➕ Ajouter une ressource</h3>
        <form method="POST">
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="titre" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Lien (URL)</label>
                <input type="url" name="lien">
            </div>
            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <option value="cours">Cours</option>
                    <option value="video">Vidéo</option>
                    <option value="article">Article</option>
                    <option value="livre">Livre</option>
                </select>
            </div>
            <div class="form-group">
                <label>Filière (optionnel)</label>
                <select name="filiere_id">
                    <option value="">-- Toutes filières --</option>
                    <?php foreach ($filieres as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <!-- Liste des ressources -->
    <div class="card">
        <h3>📋 Ressources existantes (<?= count($ressources) ?>)</h3>
        <?php if (empty($ressources)): ?>
            <p>Aucune ressource.</p>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Titre</th><th>Type</th><th>Filière</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($ressources as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['titre']) ?></strong><br><small><?= htmlspecialchars(substr($r['description'],0,60)) ?></small></td>
                        <td><?= $r['type'] ?></td>
                        <td><?= htmlspecialchars($r['filiere_nom'] ?? 'Général') ?></td>
                        <td><a href="?delete=<?= $r['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-danger">🗑️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<style>
.card { background:white; border-radius:1rem; padding:1rem; margin-bottom:1rem; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

