<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

// Récupérer tous les messages (UNIQUEMENT avec les conseillers)
try {
    $stmt = $pdo->prepare("
        SELECT m.*, 
               u.nom as expediteur_nom, 
               u.prenom as expediteur_prenom,
               u.email as expediteur_email,
               u.role as expediteur_role,
               u2.nom as destinataire_nom,
               u2.prenom as destinataire_prenom,
               u2.role as destinataire_role
        FROM messages m
        INNER JOIN utilisateurs u ON m.expediteur_id = u.id
        LEFT JOIN utilisateurs u2 ON m.destinataire_id = u2.id
        WHERE (m.expediteur_id = ? AND u.role IN ('etudiant', 'conseiller'))
           OR (m.destinataire_id = ? AND u2.role IN ('etudiant', 'conseiller'))
        ORDER BY m.date_envoi DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $erreur = "Erreur: " . $e->getMessage();
    $messages = [];
}

// Marquer un message comme lu
if (isset($_GET['marquer_lu']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE id = ? AND destinataire_id = ?");
        $stmt->execute([$id, $user_id]);
        $message = "✅ Message marqué comme lu !";
        header('Location: messages.php');
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la mise à jour";
    }
}

// Supprimer un message
if (isset($_GET['supprimer']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND (expediteur_id = ? OR destinataire_id = ?)");
        $stmt->execute([$id, $user_id, $user_id]);
        $message = "🗑️ Message supprimé !";
        header('Location: messages.php');
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la suppression";
    }
}

// Envoyer un message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['envoyer_message'])) {
    $destinataire_id = (int)$_POST['destinataire_id'];
    $sujet = nettoyer($_POST['sujet']);
    $message_texte = nettoyer($_POST['message']);
    
    if (empty($sujet)) {
        $erreur = "Le sujet est requis.";
    } elseif (empty($message_texte)) {
        $erreur = "Le message est requis.";
    } elseif (empty($destinataire_id)) {
        $erreur = "Veuillez sélectionner un destinataire.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO messages (expediteur_id, destinataire_id, sujet, message, lu, date_envoi)
                VALUES (?, ?, ?, ?, 0, NOW())
            ");
            $stmt->execute([$user_id, $destinataire_id, $sujet, $message_texte]);
            $message = "✅ Message envoyé !";
            header('Location: messages.php');
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur: " . $e->getMessage();
        }
    }
}

// Récupérer les conseillers
try {
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, role 
        FROM utilisateurs 
        WHERE role = 'conseiller' AND actif = 1
        ORDER BY nom
    ");
    $stmt->execute();
    $conseillers = $stmt->fetchAll();
} catch (PDOException $e) {
    $conseillers = [];
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* mêmes styles que admin/messages.php */
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #ffffff; font-family: 'Inter', sans-serif; }
.messages-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
.page-header { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.page-header h1 { font-size: 1.8rem; color: #1f2937; }
.btn-new-message { background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 0.7rem 1.5rem; border: none; border-radius: 0.5rem; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; }
.btn-new-message:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(5,150,105,0.3); }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: #fff; border-radius: 1rem; padding: 1.2rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); border: 1px solid #e5e7eb; text-align: center; }
.stat-number { font-size: 1.8rem; font-weight: 700; color: #059669; }
.stat-label { color: #6b7280; font-size: 0.85rem; }
.filters { background: #f9fafb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
.filter-btn { padding: 0.5rem 1.2rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; background: white; cursor: pointer; }
.filter-btn.active { background: #059669; color: white; border-color: transparent; }
.messages-list { display: flex; flex-direction: column; gap: 15px; }
.message-card { background: white; border-radius: 1rem; padding: 1.2rem; border: 1px solid #e5e7eb; transition: all 0.3s ease; }
.message-card:hover { transform: translateX(5px); border-color: #059669; }
.message-card.non_lu { background: #fef3c7; border-left: 4px solid #f59e0b; }
.message-header { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; }
.message-expediteur { font-weight: 600; }
.role-badge { background: #e5e7eb; padding: 2px 8px; border-radius: 20px; font-size: 0.7rem; margin-left: 8px; }
.message-sujet { font-weight: 600; color: #059669; margin: 8px 0; }
.message-texte { color: #4b5563; margin: 10px 0; line-height: 1.5; }
.message-date { font-size: 0.75rem; color: #9ca3af; }
.message-actions { display: flex; gap: 8px; margin-top: 10px; }
.btn-icon { background: none; border: none; padding: 0.3rem 0.8rem; cursor: pointer; border-radius: 0.4rem; font-size: 0.8rem; }
.btn-icon:hover { background: #f3f4f6; }
.btn-repondre { color: #059669; }
.btn-supprimer { color: #ef4444; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
.modal.active { display: flex; }
.modal-content { background: white; border-radius: 1rem; padding: 2rem; max-width: 500px; width: 90%; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb; }
.close-modal { background: none; border: none; font-size: 1.5rem; cursor: pointer; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.3rem; font-weight: 500; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.6rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; }
.alert-success { background: #d1fae5; color: #065f46; padding: 0.8rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 4px solid #10b981; }
.alert-error { background: #fee2e2; color: #991b1b; padding: 0.8rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 4px solid #ef4444; }
.empty-state { text-align: center; padding: 60px 20px; background: #f9fafb; border-radius: 1rem; }
.info-box { background: #dbeafe; border-left: 4px solid #3b82f6; padding: 0.8rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; color: #1e40af; display: flex; align-items: center; gap: 8px; }
@media (max-width: 768px) { .messages-container { padding: 15px; } .page-header h1 { font-size: 1.3rem; } }
</style>

<div class="messages-container">
    <div class="page-header">
        <div>
            <h1>📨 Ma messagerie</h1>
            <p>Échangez avec votre conseiller d'orientation</p>
        </div>
        <button class="btn-new-message" onclick="ouvrirModalNouveauMessage()">✨ Nouveau message</button>
    </div>

    <div class="info-box">💡 Vous pouvez envoyer et recevoir des messages uniquement avec votre conseiller d'orientation.</div>

    <?php if ($message): ?>
        <div class="alert-success"><?= $message ?></div>
    <?php endif; ?>
    <?php if ($erreur): ?>
        <div class="alert-error">⚠️ <?= $erreur ?></div>
    <?php endif; ?>

    <?php
    $total = count($messages);
    $non_lus = 0;
    foreach ($messages as $msg) {
        if ($msg['destinataire_id'] == $user_id && $msg['lu'] == 0) $non_lus++;
    }
    ?>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-number"><?= $total ?></div><div class="stat-label">Total</div></div>
        <div class="stat-card"><div class="stat-number"><?= $non_lus ?></div><div class="stat-label">Non lus</div></div>
    </div>

    <div class="filters">
        <button class="filter-btn active" data-filter="all">📋 Tous</button>
        <button class="filter-btn" data-filter="recu">📥 Reçus</button>
        <button class="filter-btn" data-filter="envoye">📤 Envoyés</button>
        <button class="filter-btn" data-filter="non_lu">🆕 Non lus</button>
    </div>

    <?php if (empty($messages)): ?>
        <div class="empty-state">
            <div style="font-size:64px;">📭</div>
            <h3>Aucun message</h3>
            <button class="btn-new-message" onclick="ouvrirModalNouveauMessage()" style="margin-top:20px;">📝 Envoyer un message</button>
        </div>
    <?php else: ?>
        <div class="messages-list">
            <?php foreach ($messages as $msg):
                $is_recu = ($msg['destinataire_id'] == $user_id);
                $status_class = ($is_recu && $msg['lu'] == 0) ? 'non_lu' : '';
            ?>
                <div class="message-card <?= $status_class ?>" data-type="<?= $is_recu ? 'recu' : 'envoye' ?>" data-lu="<?= $msg['lu'] ?>">
                    <div class="message-header">
                        <div class="message-expediteur">
                            <?php if ($is_recu): ?>
                                📥 <strong>De :</strong> <?= htmlspecialchars($msg['expediteur_prenom'] . ' ' . $msg['expediteur_nom']) ?>
                                <span class="role-badge"><?= $msg['expediteur_role'] ?></span>
                            <?php else: ?>
                                📤 <strong>À :</strong> <?= htmlspecialchars($msg['destinataire_prenom'] . ' ' . $msg['destinataire_nom']) ?>
                                <span class="role-badge"><?= $msg['destinataire_role'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="message-date">📅 <?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?></div>
                    </div>
                    <div class="message-sujet">📌 <?= htmlspecialchars($msg['sujet']) ?></div>
                    <div class="message-texte"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                    <div class="message-actions">
                        <?php if ($is_recu): ?>
                            <button class="btn-icon btn-repondre" onclick="ouvrirModalReponse(<?= $msg['expediteur_id'] ?>, '<?= htmlspecialchars($msg['sujet']) ?>')">💬 Répondre</button>
                            <?php if ($msg['lu'] == 0): ?>
                                <a href="?marquer_lu=1&id=<?= $msg['id'] ?>" class="btn-icon" style="color:#f59e0b;">✅ Marquer lu</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="?supprimer=1&id=<?= $msg['id'] ?>" class="btn-icon btn-supprimer" onclick="return confirm('Supprimer ce message ?')">🗑️ Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Nouveau Message -->
<div id="modalNouveauMessage" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>✉️ Nouveau message</h3>
            <button class="close-modal" onclick="fermerModal('modalNouveauMessage')">&times;</button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>👥 Destinataire (Conseiller) :</label>
                <select name="destinataire_id" required>
                    <option value="">Sélectionner un conseiller</option>
                    <?php foreach ($conseillers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?> (<?= $c['email'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>📌 Sujet :</label>
                <input type="text" name="sujet" required>
            </div>
            <div class="form-group">
                <label>💬 Message :</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            <button type="submit" name="envoyer_message" class="btn-new-message" style="width:100%; justify-content:center;">📨 Envoyer</button>
        </form>
    </div>
</div>

<!-- Modal Réponse -->
<div id="modalReponse" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>💬 Répondre</h3>
            <button class="close-modal" onclick="fermerModal('modalReponse')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="destinataire_id" id="reponse_destinataire_id">
            <div class="form-group">
                <label>📌 Sujet :</label>
                <input type="text" name="sujet" id="reponse_sujet" required>
            </div>
            <div class="form-group">
                <label>💬 Message :</label>
                <textarea name="message" rows="5" required></textarea>
            </div>
            <button type="submit" name="envoyer_message" class="btn-new-message" style="width:100%; justify-content:center;">📨 Envoyer</button>
        </form>
    </div>
</div>

<script>
function ouvrirModalNouveauMessage() {
    document.getElementById('modalNouveauMessage').classList.add('active');
}

function ouvrirModalReponse(destinataireId, sujet) {
    document.getElementById('reponse_destinataire_id').value = destinataireId;
    document.getElementById('reponse_sujet').value = 'RE: ' + sujet;
    document.getElementById('modalReponse').classList.add('active');
}

function fermerModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.message-card').forEach(card => {
            if (filter === 'all') card.style.display = 'block';
            else if (filter === 'non_lu') card.style.display = card.classList.contains('non_lu') ? 'block' : 'none';
            else card.style.display = card.dataset.type === filter ? 'block' : 'none';
        });
    });
});

window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>