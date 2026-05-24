<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

// Récupérer la filière de l'étudiant
$stmt = $pdo->prepare("
    SELECT p.filiere_id, f.nom as filiere_nom 
    FROM profils_etudiants p
    LEFT JOIN filieres f ON p.filiere_id = f.id
    WHERE p.etudiant_id = ?
");
$stmt->execute([$user_id]);
$filiere = $stmt->fetch();

// Vérifier si l'étudiant a une filière
if (!$filiere || !$filiere['filiere_id']) {
    $erreur = "Vous n'avez pas encore sélectionné de filière. Veuillez d'abord compléter votre profil.";
}

// Traitement des réponses
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$erreur) {
    $count = 0;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'q_') === 0) {
            $q_id = (int) str_replace('q_', '', $key);
            $reponse = nettoyer($value);
            
            $stmt = $pdo->prepare("
                INSERT INTO reponses_etudiants (etudiant_id, question_id, reponse) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE reponse = ?
            ");
            $stmt->execute([$user_id, $q_id, $reponse, $reponse]);
            $count++;
        }
    }
    if ($count > 0) {
        $message = "✅ $count réponse(s) enregistrée(s) avec succès !";
    }
}

// Récupérer les questions UNIQUEMENT pour la filière de l'étudiant (sans jointure avec competences)
$questions = [];
if (!$erreur && $filiere && $filiere['filiere_id']) {
    $stmt = $pdo->prepare("
        SELECT * FROM questionnaires 
        WHERE filiere_id = ? 
        ORDER BY id
    ");
    $stmt->execute([$filiere['filiere_id']]);
    $questions = $stmt->fetchAll();
    
    if (empty($questions)) {
        $erreur = "Aucune question n'est disponible pour votre filière pour le moment.";
    }
}

// Récupérer les réponses existantes
$reponses = [];
if (!$erreur) {
    $stmt = $pdo->prepare("SELECT question_id, reponse FROM reponses_etudiants WHERE etudiant_id = ?");
    $stmt->execute([$user_id]);
    while ($row = $stmt->fetch()) { 
        $reponses[$row['question_id']] = $row['reponse']; 
    }
}

$total = count($questions);
$repondues = count($reponses);
$progression = $total > 0 ? round(($repondues / $total) * 100) : 0;

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.questionnaire-container{max-width:800px;margin:0 auto}
.filiere-info{background:white;border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem;text-align:center;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1)}
.filiere-info h3{color:#667eea;margin-bottom:0.2rem}
.filiere-info p{font-size:0.8rem;color:#6b7280}
.progress-info{background:white;border-radius:0.75rem;padding:0.8rem 1rem;margin-bottom:1.5rem;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1)}
.progress-label{display:flex;justify-content:space-between;margin-bottom:0.3rem;font-size:0.75rem;color:#6b7280}
.progress-bar{background:#e5e7eb;border-radius:10px;height:6px;overflow:hidden}
.progress-fill{background:linear-gradient(135deg,#667eea,#764ba2);height:100%;border-radius:10px;transition:width 0.3s}
.question-card{background:white;border-radius:0.75rem;padding:1rem;margin-bottom:1rem;box-shadow:0 1px 3px 0 rgba(0,0,0,0.1)}
.question-text{font-weight:600;font-size:0.9rem;margin-bottom:0.8rem;padding-bottom:0.5rem;border-bottom:1px solid #e5e7eb}
.options-list{display:flex;flex-direction:column;gap:0.5rem}
.option-item{display:flex;align-items:center;gap:0.6rem;padding:0.5rem 0.8rem;border:1.5px solid #e5e7eb;border-radius:0.5rem;cursor:pointer;transition:all 0.2s}
.option-item:hover{border-color:#667eea;background:#f9fafb}
.option-item.selected{border-color:#667eea;background:#f5f3ff}
.option-item input[type="radio"]{width:14px;height:14px;accent-color:#667eea}
.alert-success{background:#d1fae5;color:#065f46;padding:0.8rem;border-radius:0.5rem;margin-bottom:1rem}
.alert-error{background:#fee2e2;color:#991b1b;padding:0.8rem;border-radius:0.5rem;margin-bottom:1rem}
.alert-warning{background:#fed7aa;color:#92400e;padding:0.8rem;border-radius:0.5rem;margin-bottom:1rem}
.btn-full{width:100%}
</style>

<div class="questionnaire-container">
    <?php if ($erreur): ?>
        <div class="alert alert-warning">
            ⚠️ <?= $erreur ?>
            <br><br>
            <a href="profil.php" class="btn btn-primary">🎓 Choisir ma filière</a>
        </div>
        <div style="text-align: center; margin-top: 1rem;">
           <a href="resultats.php" class="btn btn-outline">🎯 Voir mes résultats</a>
        </div>
    <?php else: ?>
        <div class="filiere-info">
            <h3>📚 Questionnaire : <?= htmlspecialchars($filiere['filiere_nom']) ?></h3>
            <p>Répondez aux questions spécifiques à votre filière</p>
        </div>
        
        <div class="progress-info">
            <div class="progress-label">
                <span>Progression</span>
                <span><?= $repondues ?> / <?= $total ?> questions</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progression ?>%"></div>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert-success"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <?php foreach ($questions as $q): ?>
                <div class="question-card">
                    <div class="question-text"><?= htmlspecialchars($q['question']) ?></div>
                    <div class="options-list">
                        <label class="option-item <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'a') ? 'selected' : '' ?>">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="a" <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'a') ? 'checked' : '' ?>>
                            A. <?= htmlspecialchars($q['option_a']) ?>
                        </label>
                        <label class="option-item <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'b') ? 'selected' : '' ?>">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="b" <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'b') ? 'checked' : '' ?>>
                            B. <?= htmlspecialchars($q['option_b']) ?>
                        </label>
                        <label class="option-item <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'c') ? 'selected' : '' ?>">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="c" <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'c') ? 'checked' : '' ?>>
                            C. <?= htmlspecialchars($q['option_c']) ?>
                        </label>
                        <label class="option-item <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'd') ? 'selected' : '' ?>">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="d" <?= (isset($reponses[$q['id']]) && $reponses[$q['id']] == 'd') ? 'checked' : '' ?>>
                            D. <?= htmlspecialchars($q['option_d']) ?>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn btn-primary btn-full">💾 Enregistrer mes réponses</button>
        </form>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.option-item').forEach(item => {
    let radio = item.querySelector('input[type="radio"]');
    if (radio && radio.checked) item.classList.add('selected');
    if (radio) radio.addEventListener('change', function() {
        item.parentElement.querySelectorAll('.option-item').forEach(i => i.classList.remove('selected'));
        if (this.checked) item.classList.add('selected');
    });
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>