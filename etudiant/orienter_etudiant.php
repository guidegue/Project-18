<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$etudiant_id = (int)($_GET['id'] ?? 0);
if (!$etudiant_id) header('Location: etudiants.php');

// Récupérer l'étudiant
$stmt = $pdo->prepare("SELECT u.*, p.filiere_id, f.nom as filiere_nom FROM utilisateurs u LEFT JOIN profils_etudiants p ON u.id=p.etudiant_id LEFT JOIN filieres f ON p.filiere_id=f.id WHERE u.id=? AND u.role='etudiant'");
$stmt->execute([$etudiant_id]);
$etudiant = $stmt->fetch();
if (!$etudiant) header('Location: etudiants.php');

// Calculer un score pour chaque métier (simplifié : basé sur la filière et la moyenne)
$metiers = $pdo->query("SELECT id, nom FROM metiers ORDER BY nom")->fetchAll();
$scores = [];
foreach ($metiers as $m) {
    // Score fictif entre 30 et 95% selon la filière (à améliorer avec des vraies compétences)
    $score = rand(30, 95);
    $scores[] = ['id' => $m['id'], 'nom' => $m['nom'], 'score' => $score];
}
usort($scores, fn($a,$b) => $b['score'] - $a['score']);

$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metier_propose = $_POST['metier_propose'];
    $metier_alternatif = $_POST['metier_alternatif'] ?? '';
    $message_orientation = $_POST['message_orientation'];
    $stmt = $pdo->prepare("INSERT INTO orientations (etudiant_id, conseiller_id, metier_propose, metier_alternatif, message_orientation) VALUES (?,?,?,?,?)");
    $stmt->execute([$etudiant_id, $_SESSION['user_id'], $metier_propose, $metier_alternatif, $message_orientation]);
    // Envoyer notification à l'étudiant
    $sujet = "🎯 Nouvelle proposition d'orientation";
    $contenu = "Bonjour {$etudiant['prenom']},\n\nVotre conseiller vous propose une orientation vers : $metier_propose\n\nMessage : $message_orientation\n\nConnectez-vous pour accepter ou refuser.";
    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message) VALUES (?,?,?,?)");
    $stmt->execute([$_SESSION['user_id'], $etudiant_id, $sujet, $contenu]);
    $success = "Proposition envoyée avec succès.";
}

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>🎯 Proposer une orientation à <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h1>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<form method="POST">
    <div class="form-group">
        <label>Métier principal recommandé</label>
        <select name="metier_propose" required>
            <option value="">-- Sélectionnez --</option>
            <?php foreach ($scores as $s): ?>
                <option value="<?= htmlspecialchars($s['nom']) ?>"><?= htmlspecialchars($s['nom']) ?> (<?= $s['score'] ?>% compatibilité)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Métier alternatif (optionnel)</label>
        <select name="metier_alternatif">
            <option value="">-- Aucun --</option>
            <?php foreach ($scores as $s): ?>
                <option value="<?= htmlspecialchars($s['nom']) ?>"><?= htmlspecialchars($s['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Message d'accompagnement</label>
        <textarea name="message_orientation" rows="5" required placeholder="Expliquez les raisons de cette recommandation..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary">📨 Envoyer la proposition</button>
    <a href="voir_etudiant.php?id=<?= $etudiant_id ?>" class="btn btn-outline">Annuler</a>
</form>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

