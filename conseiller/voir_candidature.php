<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$candidature_id = (int)($_GET['id'] ?? 0);

if ($candidature_id == 0) {
    header('Location: offres.php');
    exit;
}

// Récupérer les détails de la candidature
$stmt = $pdo->prepare("
    SELECT c.*, 
           u.nom, u.prenom, u.email, u.date_inscription,
           p.filiere_id, p.niveau, p.moyenne_generale, p.interets,
           f.nom as filiere_nom,
           o.titre, o.type, o.entreprise, o.lieu, o.description as offre_description,
           o.competences_requises, o.duree, o.date_limite
    FROM candidatures c
    JOIN utilisateurs u ON c.etudiant_id = u.id
    LEFT JOIN profils_etudiants p ON u.id = p.etudiant_id
    LEFT JOIN filieres f ON p.filiere_id = f.id
    JOIN offres o ON c.offre_id = o.id
    WHERE c.id = ?
");
$stmt->execute([$candidature_id]);
$candidature = $stmt->fetch();

if (!$candidature) {
    header('Location: offres.php');
    exit;
}

// Mettre à jour le statut de la candidature
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changer_statut'])) {
    $nouveau_statut = $_POST['statut'];
    $commentaire = $_POST['commentaire'] ?? '';
    
    $stmt = $pdo->prepare("UPDATE candidatures SET statut = ? WHERE id = ?");
    $stmt->execute([$nouveau_statut, $candidature_id]);
    $success = "✅ Statut mis à jour : " . ($nouveau_statut == 'acceptee' ? 'Candidature acceptée' : ($nouveau_statut == 'refusee' ? 'Candidature refusée' : 'En cours d\'examen'));
    
    // Envoyer une notification à l'étudiant (message automatique)
    $sujet = "Mise à jour de votre candidature - " . $candidature['titre'];
    $message_notif = "Bonjour " . $candidature['prenom'] . ",\n\n";
    $message_notif .= "Votre candidature pour l'offre \"" . $candidature['titre'] . "\" chez " . $candidature['entreprise'] . " a été mise à jour.\n\n";
    $message_notif .= "📌 Nouveau statut : ";
    switch($nouveau_statut) {
        case 'acceptee':
            $message_notif .= "✅ ACCEPTÉE\n\nFélicitations ! Nous vous contacterons très prochainement pour la suite du processus.";
            break;
        case 'refusee':
            $message_notif .= "❌ REFUSÉE\n\nNous vous remercions pour votre candidature. Malheureusement, votre profil ne correspond pas aux critères recherchés pour ce poste.";
            break;
        case 'en_cours':
            $message_notif .= "📞 EN COURS D'EXAMEN\n\nVotre candidature est actuellement en cours d'étude. Nous reviendrons vers vous rapidement.";
            break;
        default:
            $message_notif .= "⏳ EN ATTENTE\n\nVotre candidature a bien été reçue et sera étudiée prochainement.";
    }
    
    if ($commentaire) {
        $message_notif .= "\n\n💬 Commentaire du conseiller :\n" . $commentaire;
    }
    
    $message_notif .= "\n\nCordialement,\nL'équipe d'orientation";
    
    $stmt = $pdo->prepare("INSERT INTO messages (expediteur_id, destinataire_id, sujet, message, date_envoi) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $candidature['etudiant_id'], $sujet, $message_notif]);
    
    header("refresh:2;url=voir_candidature.php?id=$candidature_id");
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
.candidature-container{max-width:1000px;margin:0 auto}
.page-header{margin-bottom:2rem}
.page-header h1{font-size:1.8rem;background:linear-gradient(135deg,#667eea,#764ba2);-webkit-background-clip:text;background-clip:text;color:transparent}
.back-link{display:inline-block;margin-bottom:1rem;color:#667eea;text-decoration:none}
.back-link:hover{text-decoration:underline}
.info-card{background:white;border-radius:0.75rem;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:1.5rem;overflow:hidden}
.card-header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:0.8rem 1rem}
.card-header h3{margin:0;font-size:1rem}
.card-body{padding:1.5rem}
.info-row{display:flex;margin-bottom:0.8rem;flex-wrap:wrap}
.info-label{width:150px;font-weight:600;color:#4a5568}
.info-value{flex:1;color:#1f2937}
.badge{display:inline-block;padding:0.3rem 0.8rem;border-radius:20px;font-size:0.8rem;font-weight:600}
.badge-en_attente{background:#fed7aa;color:#92400e}
.badge-acceptee{background:#c6f6d5;color:#22543d}
.badge-refusee{background:#fed7d7;color:#9b2c2c}
.badge-en_cours{background:#bee3f8;color:#2c5282}
.status-select{padding:0.5rem;border:2px solid #e2e8f0;border-radius:0.5rem;font-size:0.9rem}
.btn-update{background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;padding:0.5rem 1rem;border-radius:0.5rem;cursor:pointer}
.btn-update:hover{transform:translateY(-1px)}
.alert-success{background:#c6f6d5;color:#22543d;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem}
.message-box{background:#f7fafc;border-left:3px solid #667eea;padding:1rem;border-radius:0.5rem;margin-top:1rem}
.message-box p{margin:0 0 0.5rem 0}
.section-title{font-weight:700;color:#1f2937;margin-bottom:0.8rem;padding-bottom:0.3rem;border-bottom:2px solid #e5e7eb}
.competence-item{display:inline-block;background:#e5e7eb;padding:0.2rem 0.6rem;border-radius:20px;font-size:0.75rem;margin:0.2rem}
@media (max-width:640px){.info-label{width:100%;margin-bottom:0.3rem}}
</style>

<div class="candidature-container">
    <a href="offres.php?onglet=offres" class="back-link">← Retour aux offres</a>
    
    <div class="page-header">
        <h1>📄 Détail de la candidature</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <!-- Informations sur l'offre -->
    <div class="info-card">
        <div class="card-header">
            <h3>📌 Informations sur l'offre</h3>
        </div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Titre :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['titre'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Type :</div>
                <div class="info-value">
                    <?php if ($candidature['type'] == 'emploi'): ?>
                        💼 Emploi
                    <?php else: ?>
                        🎓 Stage
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Entreprise :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['entreprise'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Lieu :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['lieu'] ?? 'Non précisé', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <?php if ($candidature['duree']): ?>
                <div class="info-row">
                    <div class="info-label">Durée :</div>
                    <div class="info-value"><?= htmlspecialchars($candidature['duree'], ENT_QUOTES, 'UTF-8') ?></div>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="info-label">Description :</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($candidature['offre_description'], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <?php if ($candidature['competences_requises']): ?>
                <div class="info-row">
                    <div class="info-label">Compétences requises :</div>
                    <div class="info-value">
                        <?php 
                        $competences = explode(',', $candidature['competences_requises']);
                        foreach ($competences as $comp):
                            $comp = trim($comp);
                            if ($comp):
                        ?>
                            <span class="competence-item"><?= htmlspecialchars($comp, ENT_QUOTES, 'UTF-8') ?></span>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informations sur l'étudiant -->
    <div class="info-card">
        <div class="card-header">
            <h3>👤 Informations sur le candidat</h3>
        </div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Nom complet :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['prenom'] . ' ' . $candidature['nom'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['email'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Filière :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['filiere_nom'] ?? 'Non renseignée', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Niveau :</div>
                <div class="info-value"><?= htmlspecialchars($candidature['niveau'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Moyenne :</div>
                <div class="info-value"><?= $candidature['moyenne_generale'] ? $candidature['moyenne_generale'] . '/20' : 'Non renseignée' ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Centres d'intérêt :</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($candidature['interets'] ?? 'Non renseignés', ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Membre depuis :</div>
                <div class="info-value"><?= date('d/m/Y', strtotime($candidature['date_inscription'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Détails de la candidature -->
    <div class="info-card">
        <div class="card-header">
            <h3>📝 Détails de la candidature</h3>
        </div>
        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Date de candidature :</div>
                <div class="info-value"><?= date('d/m/Y à H:i', strtotime($candidature['date_candidature'])) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut actuel :</div>
                <div class="info-value">
                    <span class="badge badge-<?= $candidature['statut'] ?>">
                        <?php 
                        switch($candidature['statut']) {
                            case 'en_attente': echo '⏳ En attente'; break;
                            case 'acceptee': echo '✅ Acceptée'; break;
                            case 'refusee': echo '❌ Refusée'; break;
                            case 'en_cours': echo '📞 En cours d\'examen'; break;
                        }
                        ?>
                    </span>
                </div>
            </div>
            <?php if ($candidature['message']): ?>
                <div class="info-row">
                    <div class="info-label">Message du candidat :</div>
                    <div class="info-value">
                        <div class="message-box">
                            <?= nl2br(htmlspecialchars($candidature['message'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulaire de mise à jour du statut -->
    <div class="info-card">
        <div class="card-header">
            <h3>✏️ Mettre à jour le statut</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="info-row">
                    <div class="info-label">Nouveau statut :</div>
                    <div class="info-value">
                        <select name="statut" class="status-select" required>
                            <option value="en_attente" <?= $candidature['statut'] == 'en_attente' ? 'selected' : '' ?>>⏳ En attente</option>
                            <option value="en_cours" <?= $candidature['statut'] == 'en_cours' ? 'selected' : '' ?>>📞 En cours d'examen</option>
                            <option value="acceptee" <?= $candidature['statut'] == 'acceptee' ? 'selected' : '' ?>>✅ Accepter la candidature</option>
                            <option value="refusee" <?= $candidature['statut'] == 'refusee' ? 'selected' : '' ?>>❌ Refuser la candidature</option>
                        </select>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Commentaire (optionnel) :</div>
                    <div class="info-value">
                        <textarea name="commentaire" rows="4" style="width:100%; padding:0.5rem; border:1px solid #e2e8f0; border-radius:0.5rem" placeholder="Ajoutez un commentaire qui sera envoyé à l'étudiant..."></textarea>
                        <small style="color:#6b7280">Ce commentaire sera envoyé automatiquement à l'étudiant par message.</small>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label"></div>
                    <div class="info-value">
                        <button type="submit" name="changer_statut" class="btn-update">📨 Mettre à jour et notifier l'étudiant</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Actions supplémentaires -->
    <div class="info-card">
        <div class="card-header">
            <h3>🔗 Actions</h3>
        </div>
        <div class="card-body">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="envoyer_message.php?id=<?= $candidature['etudiant_id'] ?>" class="btn-update" style="display:inline-block; text-decoration:none; background:#10b981">✉️ Envoyer un message à l'étudiant</a>
                <a href="voir_etudiant.php?id=<?= $candidature['etudiant_id'] ?>" class="btn-update" style="display:inline-block; text-decoration:none; background:#6b7280">👤 Voir le profil complet</a>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>