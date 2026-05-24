<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('admin');

$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='etudiant'"); $stats['etudiants'] = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role='conseiller'"); $stats['conseillers'] = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM filieres"); $stats['filieres'] = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM metiers"); $stats['metiers'] = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM ressources"); $stats['ressources'] = $stmt->fetchColumn();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.dashboard-header{margin-bottom:2rem}
.dashboard-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.5rem;margin-bottom:2rem}
.stat-card{background:white;padding:1.5rem;border-radius:1rem;display:flex;align-items:center;gap:1rem;box-shadow:0 5px 15px rgba(0,0,0,0.1)}
.stat-icon{font-size:2rem}
.stat-info h3{font-size:0.8rem;color:#718096}
.stat-value{font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.actions-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-top:2rem}
.action-card{background:white;padding:1.5rem;border-radius:1rem;text-align:center;text-decoration:none;color:#1a202c;transition:all 0.3s;box-shadow:0 2px 8px rgba(0,0,0,0.1)}
.action-card:hover{transform:translateY(-5px);background:linear-gradient(135deg,#667eea,#764ba2);color:white}
.action-icon{font-size:2rem;margin-bottom:0.5rem}
</style>

<div class="dashboard-header">
    <h1>⚙️ Administration</h1>
    <p>Bienvenue, <?= nettoyer($_SESSION['user_nom']) ?> !</p>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon">👨‍🎓</div><div class="stat-info"><h3>Étudiants</h3><div class="stat-value"><?= $stats['etudiants'] ?></div></div></div>
    <div class="stat-card"><div class="stat-icon">👔</div><div class="stat-info"><h3>Conseillers</h3><div class="stat-value"><?= $stats['conseillers'] ?></div></div></div>
    <div class="stat-card"><div class="stat-icon">🏫</div><div class="stat-info"><h3>Filières</h3><div class="stat-value"><?= $stats['filieres'] ?></div></div></div>
    <div class="stat-card"><div class="stat-icon">💼</div><div class="stat-info"><h3>Métiers</h3><div class="stat-value"><?= $stats['metiers'] ?></div></div></div>
    <div class="stat-card"><div class="stat-icon">📚</div><div class="stat-info"><h3>Ressources</h3><div class="stat-value"><?= $stats['ressources'] ?></div></div></div>
</div>

<div class="actions-grid">
    <a href="utilisateurs.php" class="action-card"><div class="action-icon">👥</div><h3>Utilisateurs</h3><p>Gérer les comptes</p></a>
    <a href="filieres.php" class="action-card"><div class="action-icon">🏫</div><h3>Filières</h3><p>Gérer les filières</p></a>
    <a href="metiers.php" class="action-card"><div class="action-icon">💼</div><h3>Métiers</h3><p>Gérer les métiers</p></a>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>