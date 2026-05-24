<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$destinataire_id = (int)($_GET['id'] ?? 0);
if (!$destinataire_id) header('Location: etudiants.php');

$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ? AND role = 'etudiant'");
$stmt->execute([$destinataire_id]);
$etudiant = $stmt->fetch();
if (!$etudiant) header('Location: etudiants.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message) VALUES (?,?,?,?)");
    $stmt->execute([$_SESSION['user_id'], $destinataire_id, $sujet, $message]);
    header('Location: voir_etudiant.php?id=' . $destinataire_id . '&sent=1');
    exit;
}

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>✉️ Envoyer un message à <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h1>
<form method="POST">
    <div class="form-group"><label>Sujet</label><input type="text" name="sujet" required></div>
    <div class="form-group"><label>Message</label><textarea name="message" rows="6" required></textarea></div>
    <button type="submit" class="btn btn-primary">Envoyer</button>
    <a href="voir_etudiant.php?id=<?= $destinataire_id ?>" class="btn btn-outline">Annuler</a>
</form>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

