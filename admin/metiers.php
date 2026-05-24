<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('admin');

$message = '';
$erreur = '';

// Ajouter un métier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $nom = nettoyer($_POST['nom']);
    $description = nettoyer($_POST['description']);
    $secteur = nettoyer($_POST['secteur']);
    if (empty($nom)) {
        $erreur = "Le nom du métier est requis.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO metiers (nom, description, secteur) VALUES (?,?,?)");
        $stmt->execute([$nom, $description, $secteur]);
        $message = "Métier ajouté.";
        header("refresh:2;url=metiers.php");
    }
}

// Supprimer un métier
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM metiers WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: metiers.php');
    exit;
}

$metiers = $pdo->query("SELECT * FROM metiers ORDER BY nom")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>💼 Gestion des métiers</h1>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-error"><?= $erreur ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:2rem;">
    <div class="card">
        <h3>➕ Ajouter un métier</h3>
        <form method="POST">
            <div class="form-group"><label>Nom *</label><input type="text" name="nom" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"></textarea></div>
            <div class="form-group"><label>Secteur (ex: Informatique, Santé...)</label><input type="text" name="secteur"></div>
            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <div class="card">
        <h3>📋 Liste des métiers (<?= count($metiers) ?>)</h3>
        <?php if (empty($metiers)): ?>
            <p>Aucun métier.</p>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Nom</th><th>Secteur</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($metiers as $m): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($m['nom']) ?></strong><br><small><?= htmlspecialchars(substr($m['description'],0,50)) ?></small></td>
                        <td><?= htmlspecialchars($m['secteur']) ?></td>
                        <td><a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-danger">🗑️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

