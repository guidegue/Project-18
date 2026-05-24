<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('conseiller');

$stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='etudiant'");
$nb_etudiants = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM ressources");
$nb_ressources = $stmt->fetchColumn();

$derniers = $pdo->query("SELECT u.id, u.nom, u.prenom, u.email, u.date_inscription FROM utilisateurs u WHERE u.role='etudiant' ORDER BY u.date_inscription DESC LIMIT 5")->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.dashboard-header{margin-bottom:2rem}
.dashboard-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:2rem}
.stat-card{background:white;padding:1.5rem;border-radius:1rem;display:flex;align-items:center;gap:1rem;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.stat-icon{font-size:2.5rem}
.stat-info h3{font-size:0.85rem;color:#718096;margin-bottom:0.25rem}
.stat-value{font-size:2rem;font-weight:700;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.actions-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin:2rem 0}
.action-card{background:white;padding:1.5rem;border-radius:1rem;text-align:center;text-decoration:none;color:#1a202c;transition:all 0.3s;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
.action-card:hover{transform:translateY(-5px);background:linear-gradient(135deg,#667eea,#764ba2);color:white}
.action-icon{font-size:2rem;margin-bottom:0.5rem}
.table-container{background:white;border-radius:1rem;overflow-x:auto;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.data-table{width:100%;border-collapse:collapse}
.data-table th{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem;text-align:left}
.data-table td{padding:1rem;border-bottom:1px solid #e2e8f0}
</style>

<div class="dashboard-header">
    <h1>👔 Espaces Conseiller</h1>
    <p>Bienvenue, <?= nettoyer($_SESSION['user_nom']) ?> !</p>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon">👨‍🎓</div><div class="stat-info"><h3>Étudiants</h3><p class="stat-value"><?= $nb_etudiants ?></p></div></div>
    <div class="stat-card"><div class="stat-icon">📚</div><div class="stat-info"><h3>Ressources</h3><p class="stat-value"><?= $nb_ressources ?></p></div></div>
</div>

<div class="actions-grid">
    <a href="etudiants.php" class="action-card"><div class="action-icon">👥</div><h3>Listes des étudiants</h3><p>Consulter et suivre</p></a>
    <a href="ressources.php" class="action-card"><div class="action-icon">📚</div><h3>Gérer les ressources</h3><p>Ajouter, modifier</p></a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead><tr><th>Étudiant</th><th>Email</th><th>Date d'inscription</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($derniers as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= date('d/m/Y', strtotime($e['date_inscription'])) ?></td>
                <td><a href="voir_etudiant.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary" style="padding:0.25rem 0.75rem;background:#667eea;color:white;text-decoration:none;border-radius:0.25rem">Voir</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>