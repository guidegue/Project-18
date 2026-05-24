<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Répondre à un message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['repondre'])) {
    $destinataire_id = (int)$_POST['destinataire_id'];
    $sujet = "RE: " . nettoyer($_POST['sujet_original']);
    $message = nettoyer($_POST['message']);
    
    // Ne pas échapper ici car on va l'insérer en DB
    $message_brut = $_POST['message'];
    
    if (empty($message_brut)) {
        $error = "Veuillez écrire votre message.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message, date_envoi) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $destinataire_id, $sujet, $message_brut]);
        $success = "✅ Votre réponse a été envoyée !";
        header("refresh:2;url=messages_recus.php");
        exit;
    }
}

// Marquer un message comme lu
if (isset($_GET['marquer_lu'])) {
    $message_id = (int)$_GET['marquer_lu'];
    $stmt = $pdo->prepare("UPDATE messages SET lu = 1 WHERE id = ? AND destinataire_id = ?");
    $stmt->execute([$message_id, $user_id]);
    header('Location: messages_recus.php');
    exit;
}

// Supprimer un message
if (isset($_GET['supprimer'])) {
    $message_id = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND destinataire_id = ?");
    $stmt->execute([$message_id, $user_id]);
    header('Location: messages_recus.php');
    exit;
}

// Récupérer TOUS les messages reçus par l'étudiant
$stmt = $pdo->prepare("
    SELECT m.*, 
           u.nom as expediteur_nom, 
           u.prenom as expediteur_prenom,
           u.role as expediteur_role
    FROM messages m
    JOIN utilisateurs u ON m.expediteur_id = u.id
    WHERE m.destinataire_id = ?
    ORDER BY m.date_envoi DESC
");
$stmt->execute([$user_id]);
$messages_recus = $stmt->fetchAll();

// Compter les messages non lus
$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0");
$stmt->execute([$user_id]);
$unread_count = $stmt->fetchColumn();

// Message à afficher pour la réponse
$repondre_a = null;
if (isset($_GET['repondre'])) {
    $id = (int)$_GET['repondre'];
    $stmt = $pdo->prepare("
        SELECT m.*, u.nom, u.prenom, u.role 
        FROM messages m
        JOIN utilisateurs u ON m.expediteur_id = u.id
        WHERE m.id = ? AND m.destinataire_id = ?
    ");
    $stmt->execute([$id, $user_id]);
    $repondre_a = $stmt->fetch();
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.messages-container{max-width:1000px;margin:0 auto}
.page-header{margin-bottom:2rem}
.page-header h1{font-size:2rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.unread-badge-header{background:#ef4444;color:white;padding:4px 10px;border-radius:20px;font-size:0.8rem;margin-left:10px}
.message-list{display:flex;flex-direction:column;gap:1rem}
.message-card{background:white;border-radius:0.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);overflow:hidden}
.message-card.unread{border-left:4px solid #ef4444;background:#fef2f2}
.message-header{display:flex;justify-content:space-between;align-items:center;padding:1rem 1rem 0.5rem 1rem;flex-wrap:wrap}
.message-expediteur{font-weight:600;color:#1f2937}
.message-date{font-size:0.8rem;color:#6b7280}
.message-sujet{padding:0 1rem;margin-bottom:0.5rem}
.message-sujet strong{color:#4a5568}
.message-contenu{padding:0 1rem 1rem 1rem;color:#4b5563;line-height:1.5;border-bottom:1px solid #e5e7eb;white-space:pre-wrap;word-wrap:break-word}
.message-actions{padding:0.75rem 1rem;display:flex;gap:0.5rem;background:#f9fafb}
.btn-sm{padding:0.25rem 0.75rem;font-size:0.8rem;border-radius:0.375rem;border:none;cursor:pointer;text-decoration:none;display:inline-block}
.btn-repondre{background:#667eea;color:white}
.btn-lu{background:#10b981;color:white}
.btn-supprimer{background:#ef4444;color:white}
.empty-state{text-align:center;padding:3rem;background:white;border-radius:0.75rem;color:#6b7280}
.reponse-form{background:white;border-radius:0.75rem;margin-top:2rem;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1)}
.reponse-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:1rem}
.reponse-body{padding:1.5rem}
.form-group{margin-bottom:1rem}
.form-group label{display:block;margin-bottom:0.5rem;font-weight:600;color:#4a5568}
.form-group textarea{width:100%;padding:0.75rem;border:2px solid #e2e8f0;border-radius:0.5rem;font-size:1rem;font-family:inherit}
.form-group textarea:focus{outline:none;border-color:#667eea}
.btn-primary{background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:0.75rem 1.5rem;border-radius:0.5rem;cursor:pointer}
.btn-secondary{background:#e2e8f0;color:#4a5568;padding:0.75rem 1.5rem;border-radius:0.5rem;text-decoration:none;display:inline-block}
.alert-success{background:#c6f6d5;color:#22543d;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.alert-error{background:#fed7d7;color:#9b2c2c;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.badge-conseiller{background:#bee3f8;color:#2c5282;padding:2px 8px;border-radius:20px;font-size:0.7rem;margin-left:8px}
.message-original{background:#f7fafc;padding:1rem;border-radius:0.5rem;margin-bottom:1rem;border-left:3px solid #667eea}
.message-original strong{color:#4a5568}
</style>

<div class="messages-container">
    <div class="page-header">
        <h1>📬 Mes messages 
            <?php if ($unread_count > 0): ?>
                <span class="unread-badge-header"><?= $unread_count ?> nouveau(x)</span>
            <?php endif; ?>
        </h1>
    </div>

    <?php if ($success): ?>
        <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($messages_recus)): ?>
        <div class="empty-state">
            <p>📭 Vous n'avez reçu aucun message pour le moment.</p>
            <p style="font-size:0.9rem; margin-top:0.5rem">Un conseiller vous contactera bientôt pour vous aider dans votre orientation.</p>
        </div>
    <?php else: ?>
        <div class="message-list">
            <?php foreach ($messages_recus as $msg): ?>
                <div class="message-card <?= $msg['lu'] == 0 ? 'unread' : '' ?>">
                    <div class="message-header">
                        <div class="message-expediteur">
                            👤 <strong><?= htmlspecialchars($msg['expediteur_prenom'] . ' ' . $msg['expediteur_nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if ($msg['expediteur_role'] == 'conseiller'): ?>
                                <span class="badge-conseiller">🎓 Conseiller</span>
                            <?php elseif ($msg['expediteur_role'] == 'admin'): ?>
                                <span class="badge-conseiller" style="background:#fefcbf; color:#975a16">👑 Admin</span>
                            <?php endif; ?>
                            <?php if ($msg['lu'] == 0): ?>
                                <span style="background:#ef4444;color:white;padding:2px 8px;border-radius:20px;font-size:11px;margin-left:8px">Nouveau</span>
                            <?php endif; ?>
                        </div>
                        <div class="message-date">📅 <?= date('d/m/Y à H:i', strtotime($msg['date_envoi'])) ?></div>
                    </div>
                    <div class="message-sujet">
                        <strong>📌 Sujet :</strong> <?= htmlspecialchars($msg['sujet'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="message-contenu">
                        <?= nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')) ?>
                    </div>
                    <div class="message-actions">
                        <a href="?repondre=<?= $msg['id'] ?>" class="btn-sm btn-repondre">✉️ Répondre</a>
                        <?php if ($msg['lu'] == 0): ?>
                            <a href="?marquer_lu=<?= $msg['id'] ?>" class="btn-sm btn-lu">✓ Marquer comme lu</a>
                        <?php endif; ?>
                        <a href="?supprimer=<?= $msg['id'] ?>" class="btn-sm btn-supprimer" onclick="return confirm('Supprimer ce message ?')">🗑 Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de réponse -->
    <?php if ($repondre_a): ?>
        <div class="reponse-form">
            <div class="reponse-header">
                <h3>✏️ Répondre à <?= htmlspecialchars($repondre_a['prenom'] . ' ' . $repondre_a['nom'], ENT_QUOTES, 'UTF-8') ?> 
                    <?php if ($repondre_a['role'] == 'conseiller'): ?>
                        <span style="font-size:0.8rem;">(Conseiller)</span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="reponse-body">
                <div class="message-original">
                    <strong>📌 Message original :</strong><br>
                    <strong>Sujet:</strong> <?= htmlspecialchars($repondre_a['sujet'], ENT_QUOTES, 'UTF-8') ?><br><br>
                    <?= nl2br(htmlspecialchars($repondre_a['message'], ENT_QUOTES, 'UTF-8')) ?>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="destinataire_id" value="<?= $repondre_a['expediteur_id'] ?>">
                    <input type="hidden" name="sujet_original" value="<?= htmlspecialchars($repondre_a['sujet'], ENT_QUOTES, 'UTF-8') ?>">
                    
                    <div class="form-group">
                        <label>💬 Votre réponse</label>
                        <textarea name="message" rows="5" required placeholder="Écrivez votre réponse ici..."></textarea>
                    </div>
                    <div style="display:flex; gap:1rem; margin-top:1rem">
                        <button type="submit" name="repondre" class="btn-primary">📨 Envoyer ma réponse</button>
                        <a href="messages_recus.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>