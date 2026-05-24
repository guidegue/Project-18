<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$id = (int)($_GET['id'] ?? 0);
if (!$id) header('Location: etudiants.php');

// Infos générales
$stmt = $pdo->prepare("
    SELECT u.*, p.*, f.nom as filiere_nom
    FROM utilisateurs u
    LEFT JOIN profils_etudiants p ON u.id = p.etudiant_id
    LEFT JOIN filieres f ON p.filiere_id = f.id
    WHERE u.id = ? AND u.role = 'etudiant'
");
$stmt->execute([$id]);
$etudiant = $stmt->fetch();
if (!$etudiant) header('Location: etudiants.php');

// Notes UE
$notes = $pdo->prepare("SELECT * FROM notes_etudiant WHERE etudiant_id = ? ORDER BY FIELD(niveau, 'L1','L2','L3','M1','M2')");
$notes->execute([$id]);
$notes = $notes->fetchAll();

// Expériences
$experiences = $pdo->prepare("SELECT * FROM experiences WHERE etudiant_id = ? ORDER BY date_debut DESC");
$experiences->execute([$id]);
$experiences = $experiences->fetchAll();

// Certifications
$certifications = $pdo->prepare("SELECT * FROM certifications WHERE etudiant_id = ? ORDER BY date_obtention DESC");
$certifications->execute([$id]);
$certifications = $certifications->fetchAll();

// Réalisations
$realisations = $pdo->prepare("SELECT * FROM realisations WHERE etudiant_id = ? ORDER BY date_realisation DESC");
$realisations->execute([$id]);
$realisations = $realisations->fetchAll();

// Objectifs
$objectifs = $pdo->prepare("SELECT * FROM objectifs_carriere WHERE etudiant_id = ? ORDER BY date_creation DESC");
$objectifs->execute([$id]);
$objectifs = $objectifs->fetchAll();

// Résultats questionnaire
$total_questions = 0;
$score = 0;
if ($etudiant['filiere_id']) {
    $total_questions = $pdo->prepare("SELECT COUNT(*) FROM questionnaires WHERE filiere_id = ?")->execute([$etudiant['filiere_id']])->fetchColumn();
    if ($total_questions > 0) {
        $bonnes = $pdo->prepare("
            SELECT COUNT(*) FROM reponses_etudiants r
            JOIN questionnaires q ON r.question_id = q.id
            WHERE r.etudiant_id = ? AND r.reponse = q.bonne_reponse
        ");
        $bonnes->execute([$id]);
        $score = round(($bonnes->fetchColumn() / $total_questions) * 100);
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<h1>👨‍🎓 <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h1>
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
    <div>
        <div class="card"><h3>📋 Informations personnelles</h3>
            <p><strong>Matricule :</strong> <?= htmlspecialchars($etudiant['matricule'] ?? '-') ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($etudiant['email']) ?></p>
            <p><strong>Téléphone :</strong> <?= htmlspecialchars($etudiant['telephone'] ?? '-') ?></p>
            <p><strong>Date naissance :</strong> <?= $etudiant['date_naissance'] ? date('d/m/Y', strtotime($etudiant['date_naissance'])) : '-' ?></p>
            <p><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($etudiant['adresse'] ?? '-')) ?></p>
            <p><strong>Année bac :</strong> <?= $etudiant['annee_bac'] ?? '-' ?></p>
        </div>
        <div class="card"><h3>🎓 Parcours académique</h3>
            <p><strong>Filière :</strong> <?= htmlspecialchars($etudiant['filiere_nom'] ?? '-') ?></p>
            <p><strong>Niveau :</strong> <?= $etudiant['niveau'] ?? '-' ?></p>
            <p><strong>Moyenne générale :</strong> <?= $etudiant['moyenne_generale'] ? $etudiant['moyenne_generale'] . '/20' : 'Non calculée' ?></p>
            <p><strong>Centres d'intérêt :</strong> <?= nl2br(htmlspecialchars($etudiant['interets'] ?? '-')) ?></p>
            <h4>📚 Unités d'enseignement</h4>
            <?php if (empty($notes)): ?><p>Aucune UE.</p>
            <?php else: ?>
                <table class="data-table"><thead><tr><th>Niveau</th><th>UE</th><th>Coeff.</th><th>Note</th></tr></thead>
                <tbody><?php foreach ($notes as $n): ?>
                    <tr><td><?= $n['niveau'] ?></td><td><?= htmlspecialchars($n['nom_ue']) ?></td><td><?= $n['coefficient'] ?></td><td><?= $n['note'] ?></td></tr>
                <?php endforeach; ?></tbody></table>
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div class="card"><h3>📊 Résultats questionnaire</h3>
            <p><strong>Score :</strong> <?= $score ?>%</p>
            <div class="progress-bar"><div class="progress-fill" style="width:<?= $score ?>%"></div></div>
        </div>
        <div class="card"><h3>💼 Expériences</h3>
            <?php if (empty($experiences)) echo "<p>Aucune.</p>";
            else foreach ($experiences as $e): ?>
                <div><strong><?= htmlspecialchars($e['titre']) ?></strong> (<?= $e['type'] ?>)<br>
                <?= htmlspecialchars($e['entreprise']) ?><br>
                <?= $e['date_debut'] ? date('d/m/Y', strtotime($e['date_debut'])) : '' ?> - <?= $e['date_fin'] ? date('d/m/Y', strtotime($e['date_fin'])) : 'Présent' ?><br>
                <small><?= nl2br(htmlspecialchars($e['description'])) ?></small></div>
            <?php endforeach; ?>
        </div>
        <div class="card"><h3>🎓 Certifications</h3>
            <?php if (empty($certifications)) echo "<p>Aucune.</p>";
            else foreach ($certifications as $c): ?>
                <div><strong><?= htmlspecialchars($c['nom']) ?></strong> - <?= htmlspecialchars($c['organisme']) ?> (<?= $c['date_obtention'] ? date('d/m/Y', strtotime($c['date_obtention'])) : '' ?>)</div>
            <?php endforeach; ?>
        </div>
        <div class="card"><h3>🏆 Réalisations</h3>
            <?php if (empty($realisations)) echo "<p>Aucune.</p>";
            else foreach ($realisations as $r): ?>
                <div><strong><?= htmlspecialchars($r['titre']) ?></strong><br><small><?= nl2br(htmlspecialchars($r['description'])) ?></small></div>
            <?php endforeach; ?>
        </div>
        <div class="card"><h3>🎯 Objectif de carrière</h3>
            <?php if (empty($objectifs)) echo "<p>Aucun.</p>";
            else foreach ($objectifs as $o): ?>
                <div><strong><?= htmlspecialchars($o['metier_cible']) ?></strong> (<?= $o['statut'] ?>)<br>
                <?= nl2br(htmlspecialchars($o['description_objectif'])) ?><br>
                <small>Créé le <?= date('d/m/Y', strtotime($o['date_creation'])) ?></small></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div style="margin-top:1.5rem;">
    <a href="orienter_etudiant.php?id=<?= $id ?>" class="btn btn-primary">🎯 Proposer une orientation</a>
    <a href="envoyer_message.php?id=<?= $id ?>" class="btn btn-primary">✉️ Envoyer un message</a>
    <a href="etudiants.php" class="btn btn-outline">← Retour</a>
</div>

<style>
.card { background:white; border-radius:1rem; padding:1rem; margin-bottom:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
.card h3 { color:#059669; margin-bottom:0.75rem; }
.progress-bar { background:#e5e7eb; border-radius:10px; height:8px; overflow:hidden; }
.progress-fill { background:#059669; height:100%; }
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

