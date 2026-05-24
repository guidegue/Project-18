<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// POSTULER à une offre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['postuler'])) {
    $offre_id = (int)$_POST['offre_id'];
    $message = $_POST['message'];
    
    // Vérifier si déjà postulé
    $stmt = $pdo->prepare("SELECT id FROM candidatures WHERE offre_id = ? AND etudiant_id = ?");
    $stmt->execute([$offre_id, $user_id]);
    if ($stmt->fetch()) {
        $error = "Vous avez déjà postulé à cette offre.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO candidatures (offre_id, etudiant_id, message, statut) VALUES (?, ?, ?, 'en_attente')");
        $stmt->execute([$offre_id, $user_id, $message]);
        $success = "✅ Votre candidature a été envoyée avec succès !";
        header("refresh:2;url=offres.php");
        exit;
    }
}

// Récupérer les offres actives
$stmt = $pdo->prepare("
    SELECT * FROM offres 
    WHERE statut = 'active' 
    AND (date_limite IS NULL OR date_limite >= CURDATE())
    ORDER BY date_publication DESC
");
$stmt->execute();
$offres = $stmt->fetchAll();

// Récupérer les candidatures de l'étudiant
$stmt = $pdo->prepare("
    SELECT c.*, o.titre, o.type, o.entreprise 
    FROM candidatures c
    JOIN offres o ON c.offre_id = o.id
    WHERE c.etudiant_id = ?
    ORDER BY c.date_candidature DESC
");
$stmt->execute([$user_id]);
$mes_candidatures = $stmt->fetchAll();

$onglet = $_GET['onglet'] ?? 'offres';

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.offres-container{max-width:1200px;margin:0 auto}
.page-header{margin-bottom:2rem}
.page-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.tabs{display:flex;gap:0.5rem;margin-bottom:1.5rem;border-bottom:2px solid #e5e7eb;flex-wrap:wrap}
.tab-btn{padding:0.75rem 1.5rem;background:none;border:none;font-size:1rem;cursor:pointer;color:#6b7280;transition:all 0.2s}
.tab-btn.active{color:#667eea;border-bottom:2px solid #667eea;margin-bottom:-2px}
.tab-content{display:none}
.tab-content.active{display:block}
.offre-card{background:white;border-radius:0.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1rem;overflow:hidden}
.offre-header{display:flex;justify-content:space-between;align-items:center;padding:1rem;border-bottom:1px solid #e5e7eb;background:#f9fafb;flex-wrap:wrap}
.offre-titre{font-weight:700;font-size:1.1rem;color:#1f2937}
.offre-type{display:inline-block;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem;margin-left:0.5rem}
.type-emploi{background:#fed7aa;color:#92400e}
.type-stage{background:#c6f6d5;color:#22543d}
.offre-body{padding:1rem}
.offre-info{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.8rem;font-size:0.85rem;color:#6b7280}
.offre-description{margin-bottom:0.8rem;color:#4b5563;line-height:1.5}
.offre-competences{background:#f3f4f6;padding:0.5rem;border-radius:0.5rem;margin-bottom:0.8rem;font-size:0.85rem}
.offre-actions{padding:0.8rem 1rem;background:#f9fafb;border-top:1px solid #e5e7eb}
.btn-postuler{background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:0.5rem 1rem;border-radius:2rem;cursor:pointer}
.btn-postuler:hover{transform:translateY(-1px)}
.btn-deja-postule{background:#c6f6d5;color:#22543d;padding:0.5rem 1rem;border-radius:2rem;display:inline-block;font-size:0.85rem}
.candidature-card{background:white;border-radius:0.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1rem;padding:1rem}
.candidature-header{display:flex;justify-content:space-between;margin-bottom:0.5rem;flex-wrap:wrap}
.candidature-titre{font-weight:700;color:#1f2937}
.badge{display:inline-block;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem}
.badge-en_attente{background:#fed7aa;color:#92400e}
.badge-acceptee{background:#c6f6d5;color:#22543d}
.badge-refusee{background:#fed7d7;color:#9b2c2c}
.badge-en_cours{background:#bee3f8;color:#2c5282}
.alert-success{background:#c6f6d5;color:#22543d;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.alert-error{background:#fed7d7;color:#9b2c2c;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.empty-state{text-align:center;padding:3rem;background:white;border-radius:0.75rem;color:#6b7280}
.form-postuler{background:#f9fafb;padding:0.8rem;border-radius:0.5rem;margin-top:0.5rem}
.form-postuler textarea{width:100%;padding:0.5rem;border:1px solid #e2e8f0;border-radius:0.5rem;margin-bottom:0.5rem}
</style>

<div class="offres-container">
    <div class="page-header">
        <h1>💼 Offres d'emploi & Stages</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert-success">✅ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-btn <?= $onglet == 'offres' ? 'active' : '' ?>" onclick="showTab('offres')">📋 Toutes les offres</button>
        <button class="tab-btn <?= $onglet == 'mes_candidatures' ? 'active' : '' ?>" onclick="showTab('mes_candidatures')">📝 Mes candidatures</button>
    </div>

    <!-- Liste des offres -->
    <div id="tab-offres" class="tab-content <?= $onglet == 'offres' ? 'active' : '' ?>">
        <?php if (empty($offres)): ?>
            <div class="empty-state">
                <p>📭 Aucune offre disponible pour le moment.</p>
                <p style="font-size:0.9rem; margin-top:0.5rem">Revenez plus tard, de nouvelles offres seront bientôt publiées.</p>
            </div>
        <?php else: ?>
            <?php foreach ($offres as $offre): 
                // Vérifier si déjà postulé
                $stmt = $pdo->prepare("SELECT id FROM candidatures WHERE offre_id = ? AND etudiant_id = ?");
                $stmt->execute([$offre['id'], $user_id]);
                $deja_postule = $stmt->fetch();
            ?>
                <div class="offre-card">
                    <div class="offre-header">
                        <div class="offre-titre">
                            <?= htmlspecialchars($offre['titre'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="offre-type <?= $offre['type'] == 'emploi' ? 'type-emploi' : 'type-stage' ?>">
                                <?= $offre['type'] == 'emploi' ? '💼 Emploi' : '🎓 Stage' ?>
                            </span>
                        </div>
                        <div class="offre-date">📅 Publiée le <?= date('d/m/Y', strtotime($offre['date_publication'])) ?></div>
                    </div>
                    <div class="offre-body">
                        <div class="offre-info">
                            <span>🏢 <?= htmlspecialchars($offre['entreprise'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span>📍 <?= htmlspecialchars($offre['lieu'] ?? 'Non précisé', ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ($offre['duree']): ?>
                                <span>⏱️ <?= htmlspecialchars($offre['duree'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if ($offre['date_limite']): ?>
                                <span>📆 Limite: <?= date('d/m/Y', strtotime($offre['date_limite'])) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="offre-description">
                            <strong>📝 Description :</strong><br>
                            <?= nl2br(htmlspecialchars($offre['description'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                        <?php if ($offre['competences_requises']): ?>
                            <div class="offre-competences">
                                <strong>⚙️ Compétences requises :</strong><br>
                                <?= nl2br(htmlspecialchars($offre['competences_requises'], ENT_QUOTES, 'UTF-8')) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="offre-actions">
                            <?php if ($deja_postule): ?>
                                <span class="btn-deja-postule">✅ Vous avez déjà postulé à cette offre</span>
                            <?php else: ?>
                                <button onclick="showPostulerForm(<?= $offre['id'] ?>)" class="btn-postuler">📝 Postuler</button>
                                <div id="postuler-form-<?= $offre['id'] ?>" class="form-postuler" style="display:none; margin-top:1rem">
                                    <form method="POST">
                                        <input type="hidden" name="offre_id" value="<?= $offre['id'] ?>">
                                        <label>💬 Message de motivation (optionnel)</label>
                                        <textarea name="message" rows="3" placeholder="Dites-nous pourquoi vous êtes intéressé par cette offre..."></textarea>
                                        <button type="submit" name="postuler" class="btn-postuler">📨 Envoyer ma candidature</button>
                                        <button type="button" onclick="hidePostulerForm(<?= $offre['id'] ?>)" style="margin-left:0.5rem; background:#e5e7eb; border:none; padding:0.5rem 1rem; border-radius:2rem; cursor:pointer">Annuler</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Mes candidatures -->
    <div id="tab-mes_candidatures" class="tab-content <?= $onglet == 'mes_candidatures' ? 'active' : '' ?>">
        <?php if (empty($mes_candidatures)): ?>
            <div class="empty-state">
                <p>📝 Vous n'avez encore postulé à aucune offre.</p>
                <p style="font-size:0.9rem; margin-top:0.5rem">Consultez les offres disponibles et postulez !</p>
            </div>
        <?php else: ?>
            <?php foreach ($mes_candidatures as $cand): ?>
                <div class="candidature-card">
                    <div class="candidature-header">
                        <span class="candidature-titre"><?= htmlspecialchars($cand['titre'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="badge badge-<?= $cand['statut'] ?>">
                            <?= $cand['statut'] == 'en_attente' ? '⏳ En attente' : ($cand['statut'] == 'acceptee' ? '✅ Acceptée' : ($cand['statut'] == 'refusee' ? '❌ Refusée' : '📞 En cours d\'examen')) ?>
                        </span>
                    </div>
                    <div style="font-size:0.85rem; color:#6b7280; margin-bottom:0.5rem">
                        <?= $cand['type'] == 'emploi' ? '💼 Emploi' : '🎓 Stage' ?> chez <strong><?= htmlspecialchars($cand['entreprise'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <?php if ($cand['message']): ?>
                        <div style="font-size:0.8rem; color:#4b5563; margin-top:0.3rem">
                            💬 <?= htmlspecialchars(substr($cand['message'], 0, 150), ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <div style="font-size:0.7rem; color:#9ca3af; margin-top:0.5rem">
                        📅 Postulé le <?= date('d/m/Y à H:i', strtotime($cand['date_candidature'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function showPostulerForm(offreId) {
    document.getElementById('postuler-form-' + offreId).style.display = 'block';
}

function hidePostulerForm(offreId) {
    document.getElementById('postuler-form-' + offreId).style.display = 'none';
}

function showTab(tab) {
    document.getElementById('tab-offres').classList.remove('active');
    document.getElementById('tab-mes_candidatures').classList.remove('active');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
    const url = new URL(window.location.href);
    url.searchParams.set('onglet', tab);
    window.history.pushState({}, '', url);
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>