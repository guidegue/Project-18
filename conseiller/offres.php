<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ajouter une offre
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_offre'])) {
    $titre = $_POST['titre'];
    $type = $_POST['type'];
    $entreprise = $_POST['entreprise'];
    $lieu = $_POST['lieu'];
    $description = $_POST['description'];
    $competences = $_POST['competences_requises'];
    $duree = $_POST['duree'];
    $date_limite = $_POST['date_limite'];
    
    $stmt = $pdo->prepare("INSERT INTO offres (titre, type, entreprise, lieu, description, competences_requises, duree, date_limite, publiee_par) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$titre, $type, $entreprise, $lieu, $description, $competences, $duree, $date_limite, $user_id]);
    $success = "✅ Offre publiée avec succès !";
    header("refresh:2;url=offres.php");
    exit;
}

// Modifier le statut d'une offre
if (isset($_GET['changer_statut'])) {
    $id = (int)$_GET['changer_statut'];
    $statut = $_GET['statut'];
    $stmt = $pdo->prepare("UPDATE offres SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);
    header('Location: offres.php');
    exit;
}

// Supprimer une offre
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM offres WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: offres.php');
    exit;
}

// Récupérer les offres
$stmt = $pdo->prepare("SELECT * FROM offres ORDER BY date_publication DESC");
$stmt->execute();
$offres = $stmt->fetchAll();

// Récupérer les candidatures par offre
$candidatures = [];
foreach ($offres as $offre) {
    $stmt = $pdo->prepare("
        SELECT c.*, u.nom, u.prenom, u.email 
        FROM candidatures c
        JOIN utilisateurs u ON c.etudiant_id = u.id
        WHERE c.offre_id = ?
        ORDER BY c.date_candidature DESC
    ");
    $stmt->execute([$offre['id']]);
    $candidatures[$offre['id']] = $stmt->fetchAll();
}

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
.form-card{background:white;border-radius:1rem;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:2rem}
.form-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem}
.form-body{padding:1.5rem}
.form-group{margin-bottom:1rem}
.form-group label{display:block;margin-bottom:0.5rem;font-weight:600;color:#4a5568}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:0.7rem;border:2px solid #e2e8f0;border-radius:0.5rem}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#667eea}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.btn-send{background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:0.6rem 1.5rem;border-radius:2rem;cursor:pointer}
.offre-card{background:white;border-radius:0.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1rem;overflow:hidden}
.offre-header{display:flex;justify-content:space-between;align-items:center;padding:1rem;border-bottom:1px solid #e5e7eb;background:#f9fafb}
.offre-titre{font-weight:700;font-size:1.1rem;color:#1f2937}
.offre-type{display:inline-block;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.7rem;margin-left:0.5rem}
.type-emploi{background:#fed7aa;color:#92400e}
.type-stage{background:#c6f6d5;color:#22543d}
.offre-body{padding:1rem}
.offre-info{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:0.8rem;font-size:0.85rem;color:#6b7280}
.offre-description{margin-bottom:0.8rem;color:#4b5563;line-height:1.5}
.offre-competences{background:#f3f4f6;padding:0.5rem;border-radius:0.5rem;margin-bottom:0.8rem;font-size:0.85rem}
.offre-actions{display:flex;gap:0.5rem;padding:0.8rem 1rem;background:#f9fafb;border-top:1px solid #e5e7eb}
.badge{display:inline-block;padding:0.2rem 0.5rem;border-radius:20px;font-size:0.7rem}
.badge-active{background:#c6f6d5;color:#22543d}
.badge-expiree{background:#fed7d7;color:#9b2c2c}
.badge-fermee{background:#e5e7eb;color:#4b5563}
.candidatures-list{margin-top:1rem;padding-top:0.5rem;border-top:1px solid #e5e7eb}
.candidat-item{padding:0.5rem;background:#f9fafb;border-radius:0.5rem;margin-bottom:0.5rem}
.alert-success{background:#c6f6d5;color:#22543d;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.alert-error{background:#fed7d7;color:#9b2c2c;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.empty-state{text-align:center;padding:3rem;background:white;border-radius:0.75rem;color:#6b7280}
@media (max-width:640px){.form-row{grid-template-columns:1fr}}
</style>

<div class="offres-container">
    <div class="page-header">
        <h1>💼 Gestion des offres (Emplois & Stages)</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert-success">✅ <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-btn <?= $onglet == 'offres' ? 'active' : '' ?>" onclick="showTab('offres')">📋 Toutes les offres</button>
        <button class="tab-btn <?= $onglet == 'nouvelle' ? 'active' : '' ?>" onclick="showTab('nouvelle')">➕ Nouvelle offre</button>
    </div>

    <!-- Liste des offres -->
    <div id="tab-offres" class="tab-content <?= $onglet == 'offres' ? 'active' : '' ?>">
        <?php if (empty($offres)): ?>
            <div class="empty-state">
                <p>📭 Aucune offre publiée pour le moment.</p>
                <p style="font-size:0.9rem; margin-top:0.5rem">Cliquez sur "Nouvelle offre" pour en créer une.</p>
            </div>
        <?php else: ?>
            <?php foreach ($offres as $offre): ?>
                <div class="offre-card">
                    <div class="offre-header">
                        <div class="offre-titre">
                            <?= htmlspecialchars($offre['titre'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="offre-type <?= $offre['type'] == 'emploi' ? 'type-emploi' : 'type-stage' ?>">
                                <?= $offre['type'] == 'emploi' ? '💼 Emploi' : '🎓 Stage' ?>
                            </span>
                            <span class="badge badge-<?= $offre['statut'] ?>">
                                <?= $offre['statut'] == 'active' ? '✅ Active' : ($offre['statut'] == 'expiree' ? '⏰ Expirée' : '🔒 Fermée') ?>
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
                        
                        <!-- Candidatures reçues -->
                        <?php if (!empty($candidatures[$offre['id']])): ?>
                            <div class="candidatures-list">
                                <strong>📋 Candidatures reçues (<?= count($candidatures[$offre['id']]) ?>) :</strong>
                                <?php foreach ($candidatures[$offre['id']] as $cand): ?>
                                    <div class="candidat-item">
                                        <div style="display:flex; justify-content:space-between; flex-wrap:wrap">
                                            <span>👤 <strong><?= htmlspecialchars($cand['prenom'] . ' ' . $cand['nom'], ENT_QUOTES, 'UTF-8') ?></strong></span>
                                            <span>📧 <?= htmlspecialchars($cand['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span>📅 <?= date('d/m/Y', strtotime($cand['date_candidature'])) ?></span>
                                            <span class="badge badge-<?= $cand['statut'] ?>">
                                                <?= $cand['statut'] == 'en_attente' ? '⏳ En attente' : ($cand['statut'] == 'acceptee' ? '✅ Acceptée' : ($cand['statut'] == 'refusee' ? '❌ Refusée' : '📞 En cours')) ?>
                                            </span>
                                        </div>
                                        <?php if ($cand['message']): ?>
                                            <div style="font-size:0.8rem; margin-top:0.3rem; color:#6b7280">
                                                💬 <?= htmlspecialchars(substr($cand['message'], 0, 100), ENT_QUOTES, 'UTF-8') ?>...
                                            </div>
                                        <?php endif; ?>
                                        <div style="margin-top:0.3rem">
                                            <a href="voir_candidature.php?id=<?= $cand['id'] ?>" style="font-size:0.75rem; color:#667eea">📄 Voir détails</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="offre-actions">
                            <a href="?changer_statut=<?= $offre['id'] ?>&statut=active" class="btn-sm btn-lu" style="background:#10b981">✅ Activer</a>
                            <a href="?changer_statut=<?= $offre['id'] ?>&statut=fermee" class="btn-sm btn-supprimer" style="background:#ef4444">🔒 Fermer</a>
                            <a href="?supprimer=<?= $offre['id'] ?>" class="btn-sm btn-supprimer" onclick="return confirm('Supprimer cette offre ?')">🗑 Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Formulaire nouvelle offre -->
    <div id="tab-nouvelle" class="tab-content <?= $onglet == 'nouvelle' ? 'active' : '' ?>">
        <div class="form-card">
            <div class="form-header">
                <h3>➕ Publier une nouvelle offre</h3>
            </div>
            <div class="form-body">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>📌 Titre de l'offre *</label>
                            <input type="text" name="titre" required placeholder="Ex: Développeur Web">
                        </div>
                        <div class="form-group">
                            <label>🏷️ Type *</label>
                            <select name="type" required>
                                <option value="stage">🎓 Stage</option>
                                <option value="emploi">💼 Emploi</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>🏢 Entreprise *</label>
                            <input type="text" name="entreprise" required placeholder="Nom de l'entreprise">
                        </div>
                        <div class="form-group">
                            <label>📍 Lieu</label>
                            <input type="text" name="lieu" placeholder="Ex: Douala, Yaoundé...">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>⏱️ Durée</label>
                            <input type="text" name="duree" placeholder="Ex: 3 mois, CDI, CDD...">
                        </div>
                        <div class="form-group">
                            <label>📆 Date limite</label>
                            <input type="date" name="date_limite">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>📝 Description détaillée *</label>
                        <textarea name="description" rows="5" required placeholder="Décrivez les missions, le contexte..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>⚙️ Compétences requises</label>
                        <textarea name="competences_requises" rows="3" placeholder="Listez les compétences nécessaires (PHP, JavaScript, Python..."></textarea>
                    </div>
                    <button type="submit" name="ajouter_offre" class="btn-send">📨 Publier l'offre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('tab-offres').classList.remove('active');
    document.getElementById('tab-nouvelle').classList.remove('active');
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
    const url = new URL(window.location.href);
    url.searchParams.set('onglet', tab);
    window.history.pushState({}, '', url);
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>