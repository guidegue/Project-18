<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('conseiller');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = nettoyer($_POST['titre']);
    $desc = nettoyer($_POST['description']);
    $lien = $_POST['lien'] ?? '';
    $type = $_POST['type'] ?? 'article';
    $stmt = $pdo->prepare("INSERT INTO ressources (titre, description, lien, type, ajoutee_par) VALUES (?,?,?,?,?)");
    $stmt->execute([$titre, $desc, $lien, $type, $_SESSION['user_id']]);
    header('Location: ressources.php');
    exit;
}
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM ressources WHERE id=?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: ressources.php');
    exit;
}
$ressources = $pdo->query("SELECT * FROM ressources ORDER BY date_ajout DESC")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.dashboard-header{margin-bottom:2rem}
.dashboard-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.two-columns{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:2rem}
.form-card{background:white;border-radius:1rem;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.form-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem 1.5rem}
.form-header h3{margin:0}
.form-body{padding:1.5rem}
.form-group{margin-bottom:1rem}
.form-group label{display:block;margin-bottom:0.25rem;font-weight:600}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:0.5rem;border:2px solid #e2e8f0;border-radius:0.5rem}
.list-card{background:white;border-radius:1rem;padding:1.5rem;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.list-item{display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid #e2e8f0}
.list-item:last-child{border-bottom:none}
.btn-danger{background:#e53e3e;color:white;padding:0.25rem 0.5rem;border-radius:0.25rem;text-decoration:none}
</style>

<div class="dashboard-header">
    <h1>📚 Gestion des ressources</h1>
</div>

<div class="two-columns">
    <div class="form-card">
        <div class="form-header"><h3>➕ Ajouter une ressource</h3></div>
        <div class="form-body">
            <form method="POST">
                <div class="form-group"><label>Titre</label><input type="text" name="titre" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="3" required></textarea></div>
                <div class="form-group"><label>Lien (URL)</label><input type="url" name="lien"></div>
                <div class="form-group"><label>Type</label><select name="type"><option value="article">Article</option><option value="video">Vidéo</option><option value="cours">Cours</option><option value="livre">Livre</option></select></div>
                <button type="submit" class="btn btn-primary btn-full">➕ Ajouter</button>
            </form>
        </div>
    </div>
    <div class="list-card">
        <h3>📋 Ressources existantes (<?= count($ressources) ?>)</h3>
        <?php foreach ($ressources as $r): ?>
        <div class="list-item">
            <div><strong><?= htmlspecialchars($r['titre']) ?></strong><br><small><?= htmlspecialchars(substr($r['description'],0,80)) ?>...</small></div>
            <a href="?delete=<?= $r['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer cette ressource ?')">🗑️</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>