<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$destinataire_id = (int)($_GET['id'] ?? 0);
$success = '';
$erreur = '';

// Récupérer le destinataire (conseiller ou admin)
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ? AND role IN ('conseiller', 'admin')");
$stmt->execute([$destinataire_id]);
$destinataire = $stmt->fetch();

if (!$destinataire) {
    header('Location: messages.php');
    exit;
}

// Récupérer le message d'origine si un ID est passé
$message_original = '';
$sujet_original = '';
if (isset($_GET['msg_id'])) {
    $msg_id = (int)$_GET['msg_id'];
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ? AND destinataire_id = ?");
    $stmt->execute([$msg_id, $user_id]);
    $msg = $stmt->fetch();
    if ($msg) {
        $sujet_original = "Re: " . $msg['sujet'];
        $message_original = "\n\n--- Message original ---\n" . $msg['message'] . "\n--- Fin du message original ---";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sujet = nettoyer($_POST['sujet']);
    $contenu = nettoyer($_POST['message']);
    
    if (empty($contenu)) {
        $erreur = "Veuillez écrire un message";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $destinataire_id, $sujet, $contenu])) {
            $success = "✅ Message envoyé avec succès !";
            // Rediriger vers la boîte de réception après 2 secondes
            header("refresh:2;url=messages.php");
        } else {
            $erreur = "Erreur lors de l'envoi du message";
        }
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.form-container {
    max-width: 600px;
    margin: 0 auto;
}
.form-card {
    background: white;
    border-radius: 1rem;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
}
.form-header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    padding: 1.2rem;
    text-align: center;
}
.form-header h1 {
    color: white;
    font-size: 1.3rem;
    margin: 0;
}
.form-header p {
    color: rgba(255,255,255,0.9);
    font-size: 0.8rem;
    margin-top: 0.3rem;
}
.form-body {
    padding: 1.5rem;
}
.form-group {
    margin-bottom: 1.2rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.85rem;
    color: #374151;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.7rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
}
.alert-success {
    background: #d1fae5;
    color: #065f46;
    padding: 0.8rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}
.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 0.8rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}
.btn-send {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 0.7rem 1.5rem;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: all 0.2s;
}
.btn-send:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.form-footer {
    margin-top: 1.5rem;
    text-align: center;
    font-size: 0.8rem;
}
.form-footer a {
    color: #667eea;
    text-decoration: none;
}
</style>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h1>✉️ Répondre à <?= htmlspecialchars($destinataire['prenom'] . ' ' . $destinataire['nom']) ?></h1>
            <p><?= htmlspecialchars($destinataire['email']) ?></p>
        </div>
        <div class="form-body">
            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
                <div class="form-footer">
                    <a href="messages.php">← Retour à mes messages</a>
                </div>
            <?php else: ?>
                <?php if ($erreur): ?>
                    <div class="alert-error">❌ <?= $erreur ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>📌 Sujet</label>
                        <input type="text" name="sujet" value="<?= htmlspecialchars($sujet_original ?: 'Réponse à votre message') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>💬 Message</label>
                        <textarea name="message" rows="6" placeholder="Écrivez votre réponse ici..." required><?= htmlspecialchars($message_original) ?></textarea>
                    </div>
                    <button type="submit" class="btn-send">📨 Envoyer la réponse</button>
                </form>
                
                <div class="form-footer">
                    <a href="messages.php">← Retour à mes messages</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>