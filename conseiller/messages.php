<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$user_id = $_SESSION['user_id'];
$message = '';
$erreur = '';

// Récupérer tous les messages
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
        WHERE m.expediteur_id = ? OR m.destinataire_id = ?
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

// Récupérer les destinataires (admins et étudiants)
try {
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, role 
        FROM utilisateurs 
        WHERE role = 'admin' AND actif = 1
        ORDER BY nom
    ");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, role 
        FROM utilisateurs 
        WHERE role = 'etudiant' AND actif = 1
        ORDER BY nom
    ");
    $stmt->execute();
    $etudiants = $stmt->fetchAll();
} catch (PDOException $e) {
    $admins = [];
    $etudiants = [];
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { background: #ffffff; font-family: 'Inter', sans-serif; }

/* Container plus compact */
.messages-container { max-width: 900px; margin: 0 auto; padding: 10px; }

/* En-tête plus compact */
.page-header { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.page-header h1 { font-size: 1.2rem; color: #1f2937; }
.page-header p { font-size: 0.7rem; color: #6b7280; margin-top: 2px; }

/* Bouton plus petit */
.btn-new-message { background: linear-gradient(135deg, #059669, #10b981); color: white; padding: 0.3rem 0.8rem; border: none; border-radius: 0.4rem; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-weight: 600; font-size: 0.75rem; }
.btn-new-message:hover { transform: translateY(-1px); box-shadow: 0 2px 5px rgba(5,150,105,0.3); }

/* Stats plus petites */
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 15px; }
.stat-card { background: #fff; border-radius: 0.5rem; padding: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); border: 1px solid #e5e7eb; text-align: center; }
.stat-number { font-size: 1.2rem; font-weight: 700; color: #059669; }
.stat-label { color: #6b7280; font-size: 0.65rem; }

/* Filtres plus compacts */
.filters { background: #f9fafb; border-radius: 0.4rem; padding: 0.4rem; margin-bottom: 12px; display: flex; gap: 4px; flex-wrap: wrap; }
.filter-btn { padding: 0.2rem 0.6rem; border: 1px solid #e5e7eb; border-radius: 0.3rem; background: white; cursor: pointer; font-size: 0.65rem; }
.filter-btn.active { background: #059669; color: white; border-color: transparent; }

/* Messages plus compacts */
.messages-list { display: flex; flex-direction: column; gap: 8px; max-height: 400px; overflow-y: auto; margin-bottom: 15px; }
.message-card { background: white; border-radius: 0.5rem; padding: 0.5rem; border: 1px solid #e5e7eb; }
.message-card.non_lu { background: #fef3c7; border-left: 3px solid #f59e0b; }
.message-header { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 5px; margin-bottom: 4px; }
.message-expediteur { font-weight: 600; font-size: 0.7rem; }
.role-badge { background: #e5e7eb; padding: 1px 4px; border-radius: 10px; font-size: 0.55rem; margin-left: 4px; }
.message-sujet { font-weight: 600; color: #059669; font-size: 0.7rem; margin: 3px 0; }
.message-texte { color: #4b5563; font-size: 0.7rem; margin: 4px 0; line-height: 1.3; }
.message-date { font-size: 0.55rem; color: #9ca3af; }
.message-actions { display: flex; gap: 4px; margin-top: 4px; }
.btn-icon { background: none; border: none; padding: 0.15rem 0.4rem; cursor: pointer; border-radius: 0.2rem; font-size: 0.6rem; }
.btn-icon:hover { background: #f3f4f6; }
.btn-repondre { color: #059669; }
.btn-supprimer { color: #ef4444; }

/* Formulaire d'envoi - TRÈS COMPACT */
.formulaire-envoi {
    background: #f9fafb;
    border-radius: 0.5rem;
    padding: 10px;
    border: 1px solid #e5e7eb;
    margin-top: 10px;
}
.formulaire-envoi h3 {
    font-size: 0.85rem;
    margin-bottom: 8px;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 5px;
}
.form-row {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}
.form-group {
    flex: 1;
}
.form-group label {
    display: block;
    margin-bottom: 2px;
    font-weight: 500;
    font-size: 0.65rem;
    color: #4b5563;
}
.form-group select, 
.form-group input, 
.form-group textarea {
    width: 100%;
    padding: 0.3rem 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.3rem;
    font-size: 0.7rem;
    font-family: inherit;
}
.form-group textarea {
    min-height: 50px;
    resize: vertical;
}
.btn-envoyer {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 0.3rem 1rem;
    border: none;
    border-radius: 0.3rem;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.7rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 5px;
}
.btn-envoyer:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(5,150,105,0.3);
}

/* Alertes */
.alert-success, .alert-error { padding: 0.4rem 0.6rem; border-radius: 0.3rem; margin-bottom: 8px; font-size: 0.7rem; }
.alert-success { background: #d1fae5; color: #065f46; border-left: 3px solid #10b981; }
.alert-error { background: #fee2e2; color: #991b1b; border-left: 3px solid #ef4444; }

.info-box { background: #dbeafe; border-left: 3px solid #3b82f6; padding: 0.4rem 0.6rem; border-radius: 0.3rem; margin-bottom: 10px; color: #1e40af; font-size: 0.65rem; display: flex; align-items: center; gap: 4px; }

/* Radio group compact */
.radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 3px;
}
.radio-option {
    display: flex;
    align-items: center;
    gap: 3px;
}
.radio-option input {
    width: 12px;
    height: 12px;
    margin: 0;
}
.radio-option label {
    font-size: 0.65rem;
    font-weight: normal;
    margin: 0;
}
.radio-category {
    margin-bottom: 5px;
}
.radio-category h4 {
    font-size: 0.7rem;
    color: #059669;
    margin-bottom: 3px;
}

@media (max-width: 600px) {
    .form-row {
        flex-direction: column;
        gap: 5px;
    }
    .form-group {
        width: 100%;
    }
}
</style>

<div class="messages-container">
    <div class="page-header">
        <div>
            <h1>📨 Messagerie</h1>
            <p>Échangez avec admins & étudiants</p>
        </div>
    </div>

    <div class="info-box">💡 Vous pouvez envoyer des messages aux administrateurs et aux étudiants.</div>

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

    <!-- Liste des messages avec scroll -->
    <div class="messages-list">
        <?php if (empty($messages)): ?>
            <div class="empty-state" style="text-align:center; padding:20px;">
                <div style="font-size:32px;">📭</div>
                <p style="font-size:0.7rem;">Aucun message</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $msg):
                $is_recu = ($msg['destinataire_id'] == $user_id);
                $status_class = ($is_recu && $msg['lu'] == 0) ? 'non_lu' : '';
            ?>
                <div class="message-card <?= $status_class ?>" data-type="<?= $is_recu ? 'recu' : 'envoye' ?>">
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
                            <button class="btn-icon btn-repondre" onclick="repondreMessage(<?= $msg['expediteur_id'] ?>, '<?= htmlspecialchars($msg['sujet']) ?>')">💬 Répondre</button>
                            <?php if ($msg['lu'] == 0): ?>
                                <a href="?marquer_lu=1&id=<?= $msg['id'] ?>" class="btn-icon" style="color:#f59e0b;">✅ Marquer lu</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <a href="?supprimer=1&id=<?= $msg['id'] ?>" class="btn-icon btn-supprimer" onclick="return confirm('Supprimer ce message ?')">🗑️ Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- FORMULAIRE D'ENVOI DE MESSAGE - TRÈS COMPACT -->
    <div class="formulaire-envoi">
        <h3>✉️ Nouveau message</h3>
        <form method="POST">
            <div class="form-row">
                <div class="form-group" style="flex:2;">
                    <label>👥 Destinataire :</label>
                    <select name="destinataire_id" required>
                        <option value="">-- Sélectionner --</option>
                        <?php if (!empty($admins)): ?>
                            <optgroup label="👑 Administrateurs">
                                <?php foreach ($admins as $admin): ?>
                                    <option value="<?= $admin['id'] ?>">Admin : <?= htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php if (!empty($etudiants)): ?>
                            <optgroup label="🎓 Étudiants">
                                <?php foreach ($etudiants as $etudiant): ?>
                                    <option value="<?= $etudiant['id'] ?>">Étudiant : <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group" style="flex:3;">
                    <label>📌 Sujet :</label>
                    <input type="text" name="sujet" placeholder="Sujet du message" required>
                </div>
            </div>
            <div class="form-group">
                <label>💬 Message :</label>
                <textarea name="message" placeholder="Écrivez votre message ici..." required></textarea>
            </div>
            <button type="submit" name="envoyer_message" class="btn-envoyer">📨 Envoyer le message</button>
        </form>
    </div>
</div>

<!-- Modal Réponse (très compact) -->
<div id="modalReponse" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:0.5rem; padding:1rem; max-width:400px; width:90%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.8rem; padding-bottom:0.3rem; border-bottom:1px solid #e5e7eb;">
            <h3 style="font-size:0.9rem;">💬 Répondre</h3>
            <button onclick="fermerModalReponse()" style="background:none; border:none; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="destinataire_id" id="reponse_destinataire_id">
            <div class="form-group">
                <label>📌 Sujet :</label>
                <input type="text" name="sujet" id="reponse_sujet" style="width:100%; padding:0.3rem; border:1px solid #e5e7eb; border-radius:0.3rem; font-size:0.7rem;" required>
            </div>
            <div class="form-group">
                <label>💬 Message :</label>
                <textarea name="message" rows="3" style="width:100%; padding:0.3rem; border:1px solid #e5e7eb; border-radius:0.3rem; font-size:0.7rem;" required></textarea>
            </div>
            <button type="submit" name="envoyer_message" class="btn-envoyer" style="width:100%; justify-content:center;">📨 Envoyer</button>
        </form>
    </div>
</div>

<script>
function repondreMessage(destinataireId, sujet) {
    document.getElementById('reponse_destinataire_id').value = destinataireId;
    document.getElementById('reponse_sujet').value = 'RE: ' + sujet;
    document.getElementById('modalReponse').style.display = 'flex';
}

function fermerModalReponse() {
    document.getElementById('modalReponse').style.display = 'none';
}

// Filtres
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

// Fermer modal en cliquant dehors
window.onclick = function(event) {
    const modal = document.getElementById('modalReponse');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>