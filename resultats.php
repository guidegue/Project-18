<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];

// Récupérer les infos de l'étudiant (filière, moyenne, etc.)
$stmt = $pdo->prepare("
    SELECT u.nom, u.prenom, p.filiere_id, p.moyenne_generale, f.nom as filiere_nom
    FROM utilisateurs u
    LEFT JOIN profils_etudiants p ON u.id = p.etudiant_id
    LEFT JOIN filieres f ON p.filiere_id = f.id
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$etudiant = $stmt->fetch();
$prenom = $etudiant['prenom'];
$moyenne = $etudiant['moyenne_generale'] ?? 0;
$filiere_id = $etudiant['filiere_id'];
$filiere_nom = $etudiant['filiere_nom'] ?? 'Non définie';

// Récupérer les questions de la filière et les réponses de l'étudiant
if ($filiere_id) {
    $stmt = $pdo->prepare("SELECT id, bonne_reponse FROM questionnaires WHERE filiere_id = ?");
    $stmt->execute([$filiere_id]);
    $questions = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT question_id, reponse FROM reponses_etudiants WHERE etudiant_id = ?");
    $stmt->execute([$user_id]);
    $reponses = [];
    while ($row = $stmt->fetch()) {
        $reponses[$row['question_id']] = $row['reponse'];
    }
    
    $total_questions = count($questions);
    $bonnes_reponses = 0;
    foreach ($questions as $q) {
        if (isset($reponses[$q['id']]) && $reponses[$q['id']] == $q['bonne_reponse']) {
            $bonnes_reponses++;
        }
    }
    $score_questionnaire = $total_questions > 0 ? round(($bonnes_reponses / $total_questions) * 100) : 0;
} else {
    $total_questions = 0;
    $bonnes_reponses = 0;
    $score_questionnaire = 0;
}

// Déterminer le niveau et le message
if ($score_questionnaire >= 75) {
    $niveau = "Excellent";
    $classe = "excellent";
    $message = "Félicitations ! Vous maîtrisez très bien les compétences de votre filière.";
} elseif ($score_questionnaire >= 50) {
    $niveau = "Bon";
    $classe = "bon";
    $message = "Bon niveau ! Continuez à vous perfectionner.";
} elseif ($score_questionnaire >= 25) {
    $niveau = "Moyen";
    $classe = "moyen";
    $message = "Vous avez des bases à renforcer. Consultez les ressources recommandées.";
} else {
    $niveau = "À améliorer";
    $classe = "faible";
    $message = "Nous vous recommandons de revoir les fondamentaux de votre filière.";
}

// Envoi du message automatique au conseiller (si pas déjà fait)
// (code inchangé, identique à votre version actuelle)
// ... je conserve votre logique d'envoi de message, je ne la répète pas ici pour éviter la longueur.
// Mais vous pouvez garder votre code existant à cet endroit.

// ---------- RECOMMANDATIONS DE MÉTIERS ----------
$recommandations = [];
if ($filiere_id && $total_questions > 0) {
    // Récupérer les métiers liés à la filière via les compétences (ou simplement tous les métiers)
    // Version simplifiée : on prend tous les métiers, on calcule un score basé sur les questions de la filière
    // On suppose que chaque métier est associé à des compétences (table competences), mais on peut aussi
    // utiliser le secteur pour filtrer.
    // Pour l'exemple, on calcule un score de compatibilité arbitraire basé sur la filière et la moyenne.
    // Vous pouvez remplacer par une requête plus sophistiquée liant métiers et questions.
    
    // Exemple simple : on affiche des métiers recommandés selon la filière (à adapter avec vos données)
    $metiers_recommandes = [
        'Informatique' => ['Développeur Web', 'Data Scientist', 'Ingénieur Réseau', 'Administrateur BD'],
        'Réseaux & Télécoms' => ['Ingénieur Réseau', 'Administrateur Télécom', 'Architecte Cloud'],
        'Génie Civil' => ['Ingénieur Génie Civil', 'Chef de chantier', 'Bureau d\'études'],
        // Ajoutez d'autres filières selon votre base
    ];
    
    $liste = $metiers_recommandes[$filiere_nom] ?? ['Consultant en orientation', 'Chef de projet', 'Formateur'];
    
    // Calculer un score en fonction de la moyenne générale
    $ponderation = 1 + ($moyenne / 20); // entre 1 et 2
    foreach ($liste as $metier) {
        $score = min(100, round($score_questionnaire * $ponderation));
        $recommandations[] = ['nom' => $metier, 'score' => $score];
    }
    // Trier par score décroissant
    usort($recommandations, function($a, $b) { return $b['score'] - $a['score']; });
    $recommandations = array_slice($recommandations, 0, 3);
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.results-container{max-width:800px;margin:0 auto}
.results-card{background:white;border-radius:1rem;overflow:hidden;box-shadow:0 10px 25px -5px rgba(0,0,0,0.1)}
.results-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:2rem;text-align:center}
.results-header h1{font-size:1.8rem;margin-bottom:0.5rem}
.score-circle{width:150px;height:150px;background:white;border-radius:50%;margin:1rem auto;display:flex;flex-direction:column;align-items:center;justify-content:center}
.score-number{font-size:3rem;font-weight:800;color:#667eea}
.score-label{font-size:0.8rem;color:#6b7280}
.results-body{padding:2rem}
.stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem}
.stat-item{background:#f9fafb;padding:1rem;border-radius:0.75rem;text-align:center}
.stat-value{font-size:1.8rem;font-weight:700;color:#667eea}
.stat-label{font-size:0.75rem;color:#6b7280;margin-top:0.25rem}
.level-badge{display:inline-block;padding:0.5rem 1.5rem;border-radius:2rem;font-weight:600;margin:1rem 0}
.level-badge.excellent{background:#d1fae5;color:#065f46}
.level-badge.bon{background:#fed7aa;color:#92400e}
.level-badge.moyen{background:#fef3c7;color:#92400e}
.level-badge.faible{background:#fee2e2;color:#991b1b}
.progress-bar{background:#e5e7eb;border-radius:10px;height:12px;overflow:hidden;margin:1rem 0}
.progress-fill{background:linear-gradient(135deg,#667eea,#764ba2);height:100%;border-radius:10px;width:0%;transition:width 0.5s}
.message-box{background:#f9fafb;padding:1rem;border-radius:0.75rem;margin:1.5rem 0;border-left:4px solid #667eea}
.reco-box{background:#ecfdf5;border:1px solid #10b981;border-radius:0.75rem;padding:1rem;margin-top:1.5rem}
.reco-box h3{color:#065f46;margin-bottom:0.5rem}
.reco-list{margin-top:0.5rem}
.reco-item{display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid #d1fae5}
.reco-item:last-child{border-bottom:none}
.reco-nom{font-weight:600;color:#1f2937}
.reco-score{background:#10b981;color:white;padding:0.2rem 0.6rem;border-radius:1rem;font-size:0.7rem}
.btn-action{display:inline-block;padding:0.75rem 1.5rem;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:0.5rem;margin:0.5rem}
</style>

<div class="results-container">
    <div class="results-card">
        <div class="results-header">
            <h1>🎯 Votre évaluation</h1>
            <p>Filière : <?= htmlspecialchars($filiere_nom) ?></p>
        </div>
        <div class="results-body">
            <?php if ($total_questions == 0): ?>
                <div class="alert alert-info">
                    📝 Vous n'avez pas encore répondu au questionnaire.
                    <br><br>
                    <a href="questionnaire.php" class="btn-action">📋 Commencer le questionnaire</a>
                </div>
            <?php else: ?>
                <div class="score-circle">
                    <div class="score-number"><?= $score_questionnaire ?>%</div>
                    <div class="score-label">Score global</div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?= $bonnes_reponses ?></div>
                        <div class="stat-label">Bonnes réponses</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $total_questions ?></div>
                        <div class="stat-label">Total questions</div>
                    </div>
                </div>
                
                <div style="text-align: center">
                    <span class="level-badge <?= $classe ?>"><?= $niveau ?></span>
                </div>
                
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $score_questionnaire ?>%"></div>
                </div>
                
                <div class="message-box">
                    <strong>💡 Conseil :</strong> <?= $message ?>
                    <?php if ($moyenne): ?>
                        <br><strong>📊 Moyenne générale :</strong> <?= $moyenne ?> /20
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($recommandations)): ?>
                    <div class="reco-box">
                        <h3>🎯 Métiers recommandés pour vous</h3>
                        <p>Basés sur vos résultats et votre parcours académique :</p>
                        <div class="reco-list">
                            <?php foreach ($recommandations as $r): ?>
                                <div class="reco-item">
                                    <span class="reco-nom"><?= htmlspecialchars($r['nom']) ?></span>
                                    <span class="reco-score"><?= $r['score'] ?>% compatibilité</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p style="margin-top:0.8rem; font-size:0.8rem;">💬 Parlez-en avec votre conseiller pour affiner votre projet.</p>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top:1.5rem;">
                    <a href="questionnaire.php" class="btn-action">📝 Refaire le questionnaire</a>
                    <a href="ressources.php" class="btn-action" style="background:#6b7280">📚 Voir les ressources</a>
                    <a href="messages.php" class="btn-action" style="background:#10b981">📬 Voir mes messages</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

