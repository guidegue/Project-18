

<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('admin');

$message = '';
$erreur = '';

// Ajouter une offre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $titre = nettoyer($_POST['titre']);
    $type = $_POST['type'];
    $entreprise = nettoyer($_POST['entreprise']);
    $lieu = nettoyer($_POST['lieu']);
    $description = nettoyer($_POST['description']);
    $competences = nettoyer($_POST['competences_requises']);
    $duree = nettoyer($_POST['duree']);
    $date_limite = $_POST['date_limite'] ?: null;
    
    if (empty($titre) || empty($entreprise)) {
        $erreur = "Le titre et l'entreprise sont requis.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO offres (titre, type, entreprise, lieu, description, competences_requises, duree, date_limite, publiee_par, statut) VALUES (?,?,?,?,?,?,?,?,?,'active')");
        $stmt->execute([$titre, $type, $entreprise, $lieu, $description, $competences, $duree, $date_limite, $_SESSION['user_id']]);
        $message = "Offre ajoutée avec succès.";
        header("refresh:2;url=offres.php");
    }
}

// Modifier le statut
if (isset($_GET['statut']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $statut = $_GET['statut'];
    $stmt = $pdo->prepare("UPDATE offres SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);
    header('Location: offres.php');
    exit;
}

// Supprimer une offre
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM offres WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: offres.php');
    exit;
}

// Récupérer toutes les offres
$offres = $pdo->query("SELECT * FROM offres ORDER BY date_publication DESC")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>💼 Gestion des offres (emplois & stages)</h1>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-error"><?= $erreur ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:2rem;">
    <!-- Formulaire d'ajout -->
    <div class="card">
        <h3>➕ Ajouter une offre</h3>
        <form method="POST">
            <div class="form-group"><label>Titre *</label><input type="text" name="titre" required></div>
            <div class="form-group"><label>Type *</label><select name="type"><option value="stage">Stage</option><option value="emploi">Emploi</option></select></div>
            <div class="form-group"><label>Entreprise *</label><input type="text" name="entreprise" required></div>
            <div class="form-group"><label>Lieu</label><input type="text" name="lieu"></div>
            <div class="form-group"><label>Description *</label><textarea name="description" rows="4" required></textarea></div>
            <div class="form-group"><label>Compétences requises</label><textarea name="competences_requises" rows="2"></textarea></div>
            <div class="form-group"><label>Durée (ex: 3 mois, CDI)</label><input type="text" name="duree"></div>
            <div class="form-group"><label>Date limite</label><input type="date" name="date_limite"></div>
            <button type="submit" name="ajouter" class="btn btn-primary">Publier</button>
        </form>
    </div>

    <!-- Liste des offres -->
    <div class="card">
        <h3>📋 Offres publiées (<?= count($offres) ?>)</h3>
        <?php if (empty($offres)): ?>
            <p>Aucune offre.</p>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Titre</th><th>Entreprise</th><th>Type</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($offres as $o): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($o['titre']) ?></strong><br><small><?= htmlspecialchars(substr($o['description'],0,50)) ?></small></td>
                        <td><?= htmlspecialchars($o['entreprise']) ?></td>
                        <td><?= $o['type'] ?></td>
                        <td><?= $o['statut'] == 'active' ? '✅ Active' : ($o['statut'] == 'expiree' ? '⏰ Expirée' : '🔒 Fermée') ?></td>
                        <td>
                            <a href="?id=<?= $o['id'] ?>&statut=active" class="btn-sm">Activer</a>
                            <a href="?id=<?= $o['id'] ?>&statut=fermee" class="btn-sm">Fermer</a>
                            <a href="?delete=<?= $o['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-sm btn-danger">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>