

<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];

// Récupération du profil avec filière
$stmt = $pdo->prepare("
    SELECT p.*, f.nom as filiere_nom 
    FROM profils_etudiants p 
    LEFT JOIN filieres f ON p.filiere_id = f.id 
    WHERE p.etudiant_id = ?
");
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

// Progression questionnaire
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reponses_etudiants WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$nb_reponses = $stmt->fetchColumn();

$total_questions = 0;
if ($profil && $profil['filiere_id']) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM questionnaires WHERE filiere_id = ?");
    $stmt->execute([$profil['filiere_id']]);
    $total_questions = $stmt->fetchColumn();
}
$progression = $total_questions > 0 ? round(($nb_reponses / $total_questions) * 100) : 0;


// Objectif en cours
$stmt = $pdo->prepare("SELECT metier_cible, statut FROM objectifs_carriere WHERE etudiant_id = ? AND statut = 'en_cours' ORDER BY date_creation DESC LIMIT 1");
$stmt->execute([$user_id]);
$objectif = $stmt->fetch();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="dashboard-header">
    <h1>👋 Bonjour, <?= nettoyer($_SESSION['user_prenom'] ?? $_SESSION['user_nom']) ?> !</h1>
    <p>Bienvenue dans votre espace d'orientation personnalisé</p>
</div>

<!-- Cartes de progression générale -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-info">
            <h3>Progression questionnaire</h3>
            <div class="stat-value"><?= $progression ?>%</div>
            <small><?= $nb_reponses ?>/<?= $total_questions ?> questions</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-info">
            <h3>Ma filière</h3>
            <div class="stat-value"><?= $profil && $profil['filiere_nom'] ? htmlspecialchars($profil['filiere_nom']) : 'À définir' ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-info">
            <h3>Moyenne générale</h3>
            <div class="stat-value"><?= $profil && $profil['moyenne_generale'] ? $profil['moyenne_generale'] . '/20' : 'Non renseignée' ?></div>
        </div>
    </div>
</div>



<?php if ($objectif): ?>
    <div class="alert alert-info">
        🎯 Objectif en cours : <strong><?= htmlspecialchars($objectif['metier_cible']) ?></strong> (statut : <?= $objectif['statut'] ?>)
        <a href="<?= BASE_URL ?>etudiant/objectifs.php" style="float:right;">Modifier</a>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        🎯 Vous n'avez pas encore défini d'objectif de carrière. <a href="<?= BASE_URL ?>etudiant/objectifs.php">Créez-en un maintenant</a>
    </div>
<?php endif; ?>

<!-- Alertes d'action -->
<?php if (!$profil || !$profil['filiere_id']): ?>
    <div class="alert alert-warning">⚠️ Votre profil est incomplet. <a href="profil.php">Complétez-le ici</a></div>
<?php endif; ?>

<?php if ($progression == 0): ?>
    <div class="alert alert-info">🎯 Commencez votre évaluation ! <a href="questionnaire.php">Passer le questionnaire</a></div>
<?php elseif ($progression < 100): ?>
    <div class="alert alert-info">📝 Vous êtes à <?= $progression ?>% du questionnaire. <a href="questionnaire.php">Continuer</a></div>
<?php else: ?>
    <div class="alert alert-success">✅ Félicitations ! <a href="resultats.php">Voir vos résultats et recommandations</a></div>
<?php endif; ?>

<!-- Actions rapides -->
<div class="actions-grid">
    <a href="profil.php" class="action-card"><div class="action-icon">👤</div><h3>Mon profil</h3><p>Informations personnelles</p></a>
    <a href="parcours.php" class="action-card"><div class="action-icon">📚</div><h3>Mon parcours</h3><p>Saisir mes notes par UE</p></a>
    <a href="questionnaire.php" class="action-card"><div class="action-icon">📝</div><h3>Questionnaire</h3><p>Évaluer mes compétences</p></a>
    <a href="resultats.php" class="action-card"><div class="action-icon">🎯</div><h3>Résultats</h3><p>Voir mon score & métiers</p></a>
    <a href="ressources.php" class="action-card"><div class="action-icon">📖</div><h3>Ressources</h3><p>Se former</p></a>
    <a href="offres.php" class="action-card"><div class="action-icon">💼</div><h3>Offres</h3><p>Emplois & stages</p></a>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>