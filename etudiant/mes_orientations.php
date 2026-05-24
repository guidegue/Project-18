<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];

// Traiter la réponse de l'étudiant
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['orientation_id'])) {
    $orientation_id = (int)$_POST['orientation_id'];
    $action = $_POST['action'];
    
    $stmt = $pdo->prepare("UPDATE orientations SET statut = ?, date_reponse = NOW() WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$action, $orientation_id, $user_id]);
    
    $message = $action == 'accepte' ? "✅ Vous avez accepté cette orientation !" : "❌ Vous avez refusé cette orientation.";
    
    // Envoyer une notification au conseiller
    $stmt = $pdo->prepare("SELECT conseiller_id, metier_propose FROM orientations WHERE id = ?");
    $stmt->execute([$orientation_id]);
    $orientation = $stmt->fetch();
    
    $sujet = "L'étudiant a répondu à votre proposition";
    $contenu = "L'étudiant a " . ($action == 'accepte' ? "accepté" : "refusé") . " la proposition d'orientation vers " . $orientation['metier_propose'];
    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $orientation['conseiller_id'], $sujet, $contenu]);
}

// Récupérer les orientations proposées
$stmt = $pdo->prepare("
    SELECT o.*, u.nom, u.prenom, u.role
    FROM orientations o
    JOIN utilisateurs u ON o.conseiller_id = u.id
    WHERE o.etudiant_id = ?
    ORDER BY o.date_proposition DESC
");
$stmt->execute([$user_id]);
$orientations = $stmt->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.orientation-container {
    max-width: 800px;
    margin: 0 auto;
}
.orientation-header {
    text-align: center;
    margin-bottom: 2rem;
}
.orientation-header h1 {
    color: white;
    font-size: 1.8rem;
}
.orientation-card {
    background: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1);
    border-left: 4px solid #667eea;
}
.orientation-card.accepte {
    border-left-color: #10b981;
}
.orientation-card.refuse {
    border-left-color: #ef4444;
}
.orientation-title {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}
.orientation-conseiller {
    color: #6b7280;
    font-size: 0.8rem;
    margin-bottom: 1rem;
}
.orientation-message {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    margin: 1rem 0;
}
.orientation-status {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 2rem;
    font-size: 0.7rem;
    font-weight: 600;
}
.status-propose {
    background: #fef3c7;
    color: #92400e;
}
.status-accepte {
    background: #d1fae5;
    color: #065f46;
}
.status-refuse {
    background: #fee2e2;
    color: #991b1b;
}
.btn-accept {
    background: #10b981;
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    margin-right: 0.5rem;
}
.btn-refuse {
    background: #ef4444;
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
}
.alert-info {
    background: #dbeafe;
    color: #1e40af;
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
}
</style>

<div class="orientation-container">
    <div class="orientation-header">
        <h1>🎯 Mes orientations proposées</h1>
        <p>Votre conseiller vous guide vers votre avenir professionnel</p>
    </div>
    
    <?php if (empty($orientations)): ?>
        <div class="alert-info">
            📭 Aucune proposition d'orientation pour le moment.
            <br><br>
            <small>Votre conseiller vous fera des propositions après analyse de vos résultats.</small>
        </div>
    <?php else: ?>
        <?php foreach ($orientations as $o): ?>
            <div class="orientation-card <?= $o['statut'] ?>">
                <div class="orientation-title">
                    🎯 <?= htmlspecialchars($o['metier_propose']) ?>
                    <?php if ($o['metier_alternatif']): ?>
                        <br><small>🔄 Alternativement: <?= htmlspecialchars($o['metier_alternatif']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="orientation-conseiller">
                    👔 Proposé par <?= htmlspecialchars($o['prenom'] . ' ' . $o['nom']) ?> 
                    (<?= date('d/m/Y', strtotime($o['date_proposition'])) ?>)
                </div>
                <div class="orientation-message">
                    <?= nl2br(htmlspecialchars($o['message_orientation'])) ?>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <span class="orientation-status status-<?= $o['statut'] ?>">
                        <?php if ($o['statut'] == 'propose'): ?>⏳ En attente de votre réponse
                        <?php elseif ($o['statut'] == 'accepte'): ?>✅ Vous avez accepté cette orientation
                        <?php else: ?>❌ Vous avez refusé cette orientation
                        <?php endif; ?>
                    </span>
                    
                    <?php if ($o['statut'] == 'propose'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="orientation_id" value="<?= $o['id'] ?>">
                            <button type="submit" name="action" value="accepte" class="btn-accept">✅ Accepter</button>
                            <button type="submit" name="action" value="refuse" class="btn-refuse">❌ Refuser</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>