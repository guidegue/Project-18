<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO filieres (nom, description) VALUES (?, ?)");
    $stmt->execute([nettoyer($_POST['nom']), nettoyer($_POST['description'])]);
    header('Location: filieres.php');
    exit;
}
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM filieres WHERE id=?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: filieres.php');
    exit;
}
$filieres = $pdo->query("SELECT * FROM filieres ORDER BY nom")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.dashboard-header{margin-bottom:2rem}
.dashboard-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.two-columns{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:2rem}
.form-card{background:white;border-radius:1rem;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.form-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem 1.5rem}
.form-body{padding:1.5rem}
.list-card{background:white;border-radius:1rem;padding:1.5rem;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.list-item{display:flex;justify-content:space-between;align-items:center;padding:0.75rem 0;border-bottom:1px solid #e2e8f0}
.list-item:last-child{border-bottom:none}
.btn-danger{background:#e53e3e;color:white;padding:0.25rem 0.5rem;border-radius:0.25rem;text-decoration:none}
</style>

<div class="dashboard-header">
    <h1>🏫 Gestion des filières</h1>
</div>

<div class="two-columns">
    <div class="form-card">
        <div class="form-header"><h3>➕ Ajouter une filière</h3></div>
        <div class="form-body">
            <form method="POST">
                <div class="form-group"><label>Nom</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
                <button type="submit" class="btn btn-primary btn-full">➕ Ajouter</button>
            </form>
        </div>
    </div>
    <div class="list-card">
        <h3>📋 Liste des filières (<?= count($filieres) ?>)</h3>
        <?php foreach ($filieres as $f): ?>
        <div class="list-item">
            <div><strong><?= htmlspecialchars($f['nom']) ?></strong><br><small><?= htmlspecialchars(substr($f['description'],0,80)) ?></small></div>
            <a href="?delete=<?= $f['id'] ?>" class="btn-danger" onclick="return confirm('Supprimer cette filière ?')">🗑️</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>