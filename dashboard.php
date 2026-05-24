<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];

// Vérifier si le profil existe et si les champs obligatoires sont remplis
$stmt = $pdo->prepare("SELECT * FROM profils_etudiants WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

$profil_complet = false;
$filiere_ok = false;
if ($profil && !empty($profil['filiere_id']) && !empty($profil['niveau'])) {
    $filiere_ok = true;
    // On considère le profil complet si les infos perso de base sont là (matricule, etc.)
    if (!empty($profil['matricule'])) {
        $profil_complet = true;
    }
}

// Progression questionnaire
$nb_reponses = 0;
$total_questions = 0;
$questionnaire_fait = false;
if ($filiere_ok) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reponses_etudiants WHERE etudiant_id = ?");
    $stmt->execute([$user_id]);
    $nb_reponses = $stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM questionnaires WHERE filiere_id = ?");
    $stmt->execute([$profil['filiere_id']]);
    $total_questions = $stmt->fetchColumn();
    if ($total_questions > 0 && $nb_reponses >= $total_questions) {
        $questionnaire_fait = true;
    }
}

// Vérifier si l'étudiant a déjà saisi des notes UE
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notes_etudiant WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$nb_ue = $stmt->fetchColumn();
$parcours_renseigne = ($nb_ue > 0);

// Récupération des autres compteurs (expériences, etc.)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM experiences WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$nb_exp = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM certifications WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$nb_certif = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM realisations WHERE etudiant_id = ?");
$stmt->execute([$user_id]);
$nb_real = $stmt->fetchColumn();

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
            <div class="stat-value"><?= $total_questions > 0 ? round(($nb_reponses / $total_questions) * 100) : 0 ?>%</div>
            <small><?= $nb_reponses ?>/<?= $total_questions ?> questions</small>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-info">
            <h3>Ma filière</h3>
            <div class="stat-value"><?= $profil && $profil['filiere_id'] ? htmlspecialchars($profil['filiere_nom'] ?? 'À définir') : 'À définir' ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-info">
            <h3>Moyenne générale</h3>
            <div class="stat-value"><?= $profil && $profil['moyenne_generale'] ? $profil['moyenne_generale'] . '/20' : 'Non calculée' ?></div>
        </div>
    </div>
</div>

<!-- ALERTES GUIDÉES (non clignotantes, avec actions) -->
<div class="alerts-container" style="margin-bottom: 2rem;">
    <?php if (!$profil_complet): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Étape 1 : Complétez vos informations personnelles</strong><br>
            Veuillez renseigner votre matricule, date de naissance, etc. pour bénéficier d'une orientation personnalisée.
            <br><a href="profil.php" class="btn btn-sm btn-primary" style="margin-top:0.5rem; display:inline-block;">✏️ Compléter mon profil</a>
        </div>
    <?php elseif (!$questionnaire_fait): ?>
        <div class="alert alert-info">
            <strong>📝 Étape 2 : Évaluez vos compétences</strong><br>
            Le questionnaire vous permettra de mesurer votre niveau et d'obtenir des recommandations de métiers.
            <br><a href="questionnaire.php" class="btn btn-sm btn-primary" style="margin-top:0.5rem; display:inline-block;">🚀 Passer le test</a>
        </div>
    <?php elseif (!$parcours_renseigne): ?>
        <div class="alert alert-success">
            <strong>🎯 Étape 3 : Affinez votre profil académique (optionnel mais recommandé)</strong><br>
            Ajoutez vos notes par unité d'enseignement pour que votre moyenne générale soit calculée et affiner les recommandations.
            <br><a href="parcours.php" class="btn btn-sm btn-primary" style="margin-top:0.5rem; display:inline-block;">📚 Saisir mon parcours</a>
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            ✅ Félicitations ! Votre profil est complet. Consultez vos <a href="resultats.php">résultats</a> et <a href="progression.php">suivez votre progression</a>.
        </div>
    <?php endif; ?>
</div>

<!-- Cartes suivi de carrière -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">💼</div>
        <div class="stat-info">
            <h3>Expériences</h3>
            <div class="stat-value"><?= $nb_exp ?></div>
            <a href="progression.php?onglet=experiences">Gérer</a>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-info">
            <h3>Certifications</h3>
            <div class="stat-value"><?= $nb_certif ?></div>
            <a href="progression.php?onglet=certifications">Gérer</a>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🏆</div>
        <div class="stat-info">
            <h3>Réalisations</h3>
            <div class="stat-value"><?= $nb_real ?></div>
            <a href="progression.php?onglet=realisations">Gérer</a>
        </div>
    </div>
</div>

<?php if ($objectif): ?>
    <div class="alert alert-info">
        🎯 Objectif en cours : <strong><?= htmlspecialchars($objectif['metier_cible']) ?></strong> (statut : <?= $objectif['statut'] ?>)
        <a href="progression.php?onglet=objectifs" style="float:right;">Modifier</a>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        🎯 Vous n'avez pas encore défini d'objectif de carrière. <a href="progression.php?onglet=objectifs">Créez-en un maintenant</a>
    </div>
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