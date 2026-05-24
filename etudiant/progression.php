<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$onglet = $_GET['onglet'] ?? 'experiences';
$erreur = '';
$succes = '';

// ---------- GESTION DES EXPÉRIENCES ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_experience'])) {
    $titre = nettoyer($_POST['titre']);
    $entreprise = nettoyer($_POST['entreprise']);
    $type = $_POST['type'];
    $debut = $_POST['date_debut'] ?: null;
    $fin = $_POST['date_fin'] ?: null;
    $desc = nettoyer($_POST['description']);
    if (empty($titre)) $erreur = "Le titre de l'expérience est requis.";
    else {
        $stmt = $pdo->prepare("INSERT INTO experiences (etudiant_id, titre, entreprise, type, date_debut, date_fin, description) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$user_id, $titre, $entreprise, $type, $debut, $fin, $desc]);
        header("Location: progression.php?onglet=experiences&success=1");
        exit;
    }
}
if (isset($_GET['supprimer_exp'])) {
    $id = (int)$_GET['supprimer_exp'];
    $stmt = $pdo->prepare("DELETE FROM experiences WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: progression.php?onglet=experiences");
    exit;
}

// ---------- GESTION DES CERTIFICATIONS ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_certification'])) {
    $nom = nettoyer($_POST['nom_certif']);
    $organisme = nettoyer($_POST['organisme']);
    $date_obt = $_POST['date_obtention'] ?: null;
    $lien = nettoyer($_POST['lien_justificatif']);
    if (empty($nom)) $erreur = "Le nom de la certification est requis.";
    else {
        $stmt = $pdo->prepare("INSERT INTO certifications (etudiant_id, nom, organisme, date_obtention, lien_justificatif) VALUES (?,?,?,?,?)");
        $stmt->execute([$user_id, $nom, $organisme, $date_obt, $lien]);
        header("Location: progression.php?onglet=certifications&success=1");
        exit;
    }
}
if (isset($_GET['supprimer_cert'])) {
    $id = (int)$_GET['supprimer_cert'];
    $stmt = $pdo->prepare("DELETE FROM certifications WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: progression.php?onglet=certifications");
    exit;
}

// ---------- GESTION DES RÉALISATIONS ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_realisation'])) {
    $titre = nettoyer($_POST['titre_real']);
    $description = nettoyer($_POST['description_real']);
    $date_real = $_POST['date_realisation'] ?: null;
    $lien = nettoyer($_POST['lien_real']);
    if (empty($titre)) $erreur = "Le titre de la réalisation est requis.";
    else {
        $stmt = $pdo->prepare("INSERT INTO realisations (etudiant_id, titre, description, date_realisation, lien) VALUES (?,?,?,?,?)");
        $stmt->execute([$user_id, $titre, $description, $date_real, $lien]);
        header("Location: progression.php?onglet=realisations&success=1");
        exit;
    }
}
if (isset($_GET['supprimer_real'])) {
    $id = (int)$_GET['supprimer_real'];
    $stmt = $pdo->prepare("DELETE FROM realisations WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: progression.php?onglet=realisations");
    exit;
}

// ---------- GESTION DES OBJECTIFS ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter_objectif'])) {
    $metier = nettoyer($_POST['metier_cible']);
    $description = nettoyer($_POST['description_objectif']);
    if (empty($metier)) $erreur = "Le métier cible est requis.";
    else {
        $stmt = $pdo->prepare("SELECT id FROM objectifs_carriere WHERE etudiant_id = ? AND statut = 'en_cours'");
        $stmt->execute([$user_id]);
        $existant = $stmt->fetch();
        if ($existant) {
            $stmt = $pdo->prepare("UPDATE objectifs_carriere SET metier_cible = ?, description_objectif = ? WHERE id = ?");
            $stmt->execute([$metier, $description, $existant['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO objectifs_carriere (etudiant_id, metier_cible, description_objectif) VALUES (?,?,?)");
            $stmt->execute([$user_id, $metier, $description]);
        }
        header("Location: progression.php?onglet=objectifs&success=1");
        exit;
    }
}
if (isset($_GET['changer_statut'])) {
    $id = (int)$_GET['changer_statut'];
    $statut = $_GET['statut'];
    $stmt = $pdo->prepare("UPDATE objectifs_carriere SET statut = ? WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$statut, $id, $user_id]);
    header("Location: progression.php?onglet=objectifs");
    exit;
}

// Récupération des données
$experiences = $pdo->prepare("SELECT * FROM experiences WHERE etudiant_id = ? ORDER BY date_debut DESC");
$experiences->execute([$user_id]);
$experiences = $experiences->fetchAll();

$certifications = $pdo->prepare("SELECT * FROM certifications WHERE etudiant_id = ? ORDER BY date_obtention DESC");
$certifications->execute([$user_id]);
$certifications = $certifications->fetchAll();

$realisations = $pdo->prepare("SELECT * FROM realisations WHERE etudiant_id = ? ORDER BY date_realisation DESC");
$realisations->execute([$user_id]);
$realisations = $realisations->fetchAll();

$objectifs = $pdo->prepare("SELECT * FROM objectifs_carriere WHERE etudiant_id = ? ORDER BY date_creation DESC");
$objectifs->execute([$user_id]);
$objectifs = $objectifs->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<h1>📈 Ma progression professionnelle</h1>
<p>Gérez vos expériences, certifications, réalisations et objectif de carrière.</p>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">✅ Opération réussie.</div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="alert alert-error">❌ <?= $erreur ?></div>
<?php endif; ?>

<!-- Onglets -->
<div class="tabs" style="display:flex; gap:0.5rem; border-bottom:1px solid #e5e7eb; margin-bottom:1.5rem; flex-wrap:wrap;">
    <a href="?onglet=experiences" class="tab-btn <?= $onglet == 'experiences' ? 'active' : '' ?>">💼 Expériences</a>
    <a href="?onglet=certifications" class="tab-btn <?= $onglet == 'certifications' ? 'active' : '' ?>">🎓 Certifications</a>
    <a href="?onglet=realisations" class="tab-btn <?= $onglet == 'realisations' ? 'active' : '' ?>">🏆 Réalisations</a>
    <a href="?onglet=objectifs" class="tab-btn <?= $onglet == 'objectifs' ? 'active' : '' ?>">🎯 Objectif carrière</a>
</div>

<style>
.tab-btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #6b7280;
    border-bottom: 2px solid transparent;
}
.tab-btn.active {
    color: #059669;
    border-bottom-color: #059669;
}
</style>

<!-- Expériences -->
<div id="experiences" style="display: <?= $onglet == 'experiences' ? 'block' : 'none' ?>;">
    <div style="background:white; padding:1rem; border-radius:1rem; margin-bottom:1rem;">
        <h3>➕ Ajouter une expérience</h3>
        <form method="POST">
            <div class="form-row">
                <input type="text" name="titre" placeholder="Titre *" required style="flex:2;">
                <input type="text" name="entreprise" placeholder="Entreprise" style="flex:2;">
                <select name="type" style="flex:1;">
                    <option value="stage">Stage</option><option value="emploi">Emploi</option>
                    <option value="benevolat">Bénévolat</option><option value="autre">Autre</option>
                </select>
            </div>
            <div class="form-row">
                <input type="date" name="date_debut"> <span>à</span> <input type="date" name="date_fin">
            </div>
            <textarea name="description" rows="2" placeholder="Description (optionnel)"></textarea>
            <button type="submit" name="ajouter_experience" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <?php if (empty($experiences)): ?>
        <div class="alert alert-info">Aucune expérience enregistrée.</div>
    <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Titre</th><th>Entreprise</th><th>Type</th><th>Période</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($experiences as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e['titre']) ?></td>
                    <td><?= htmlspecialchars($e['entreprise']) ?></td>
                    <td><?= $e['type'] ?></td>
                    <td><?= $e['date_debut'] ? date('d/m/Y', strtotime($e['date_debut'])) : '' ?> - <?= $e['date_fin'] ? date('d/m/Y', strtotime($e['date_fin'])) : 'Présent' ?></td>
                    <td><a href="?supprimer_exp=<?= $e['id'] ?>&onglet=experiences" onclick="return confirm('Supprimer ?')">🗑️</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Certifications -->
<div id="certifications" style="display: <?= $onglet == 'certifications' ? 'block' : 'none' ?>;">
    <div style="background:white; padding:1rem; border-radius:1rem; margin-bottom:1rem;">
        <h3>➕ Ajouter une certification</h3>
        <form method="POST">
            <input type="text" name="nom_certif" placeholder="Nom de la certification *" required style="width:100%; margin-bottom:0.5rem;">
            <input type="text" name="organisme" placeholder="Organisme délivreur" style="width:100%; margin-bottom:0.5rem;">
            <input type="date" name="date_obtention" style="width:100%; margin-bottom:0.5rem;">
            <input type="url" name="lien_justificatif" placeholder="Lien vers le justificatif (optionnel)" style="width:100%; margin-bottom:0.5rem;">
            <button type="submit" name="ajouter_certification" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <?php if (empty($certifications)): ?>
        <div class="alert alert-info">Aucune certification enregistrée.</div>
    <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Nom</th><th>Organisme</th><th>Date d'obtention</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($certifications as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['organisme']) ?></td>
                    <td><?= $c['date_obtention'] ? date('d/m/Y', strtotime($c['date_obtention'])) : '' ?></td>
                    <td><a href="?supprimer_cert=<?= $c['id'] ?>&onglet=certifications" onclick="return confirm('Supprimer ?')">🗑️</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Réalisations -->
<div id="realisations" style="display: <?= $onglet == 'realisations' ? 'block' : 'none' ?>;">
    <div style="background:white; padding:1rem; border-radius:1rem; margin-bottom:1rem;">
        <h3>➕ Ajouter une réalisation</h3>
        <form method="POST">
            <input type="text" name="titre_real" placeholder="Titre de la réalisation *" required style="width:100%; margin-bottom:0.5rem;">
            <textarea name="description_real" rows="2" placeholder="Description"></textarea>
            <input type="date" name="date_realisation" style="width:100%; margin-bottom:0.5rem;">
            <input type="url" name="lien_real" placeholder="Lien (projet, portfolio, etc.)" style="width:100%; margin-bottom:0.5rem;">
            <button type="submit" name="ajouter_realisation" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <?php if (empty($realisations)): ?>
        <div class="alert alert-info">Aucune réalisation enregistrée.</div>
    <?php else: ?>
        <table class="data-table">
            <thead><tr><th>Titre</th><th>Description</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($realisations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['titre']) ?></td>
                    <td><?= htmlspecialchars(substr($r['description'],0,50)) ?></td>
                    <td><?= $r['date_realisation'] ? date('d/m/Y', strtotime($r['date_realisation'])) : '' ?></td>
                    <td><a href="?supprimer_real=<?= $r['id'] ?>&onglet=realisations" onclick="return confirm('Supprimer ?')">🗑️</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Objectif carrière -->
<div id="objectifs" style="display: <?= $onglet == 'objectifs' ? 'block' : 'none' ?>;">
    <div style="background:white; padding:1rem; border-radius:1rem; margin-bottom:1rem;">
        <h3>🎯 Définir mon objectif de carrière</h3>
        <form method="POST">
            <input type="text" name="metier_cible" placeholder="Métier visé (ex: Data Scientist, Développeur Web)" required style="width:100%; margin-bottom:0.5rem;">
            <textarea name="description_objectif" rows="3" placeholder="Description de votre objectif, étapes à franchir..."></textarea>
            <button type="submit" name="ajouter_objectif" class="btn btn-primary">Enregistrer mon objectif</button>
        </form>
    </div>
    <?php if (empty($objectifs)): ?>
        <div class="alert alert-info">Aucun objectif défini. Créez-en un ci-dessus.</div>
    <?php else: ?>
        <?php foreach ($objectifs as $o): ?>
            <div class="objectif-item" style="border:1px solid #e5e7eb; border-radius:1rem; padding:1rem; margin-bottom:1rem; background:white;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin:0;">🎯 <?= htmlspecialchars($o['metier_cible']) ?></h3>
                    <span class="badge badge-<?= $o['statut'] ?>" style="background:<?= $o['statut']=='en_cours'?'#fef3c7':($o['statut']=='atteint'?'#d1fae5':'#fee2e2') ?>; padding:0.2rem 0.8rem; border-radius:1rem;">
                        <?= $o['statut'] == 'en_cours' ? 'En cours' : ($o['statut'] == 'atteint' ? 'Atteint' : 'Abandonné') ?>
                    </span>
                </div>
                <p><?= nl2br(htmlspecialchars($o['description_objectif'])) ?></p>
                <small>Créé le <?= date('d/m/Y', strtotime($o['date_creation'])) ?></small><br>
                <div style="margin-top:0.5rem;">
                    <a href="?changer_statut=<?= $o['id'] ?>&statut=en_cours&onglet=objectifs" class="btn-sm">🟢 En cours</a>
                    <a href="?changer_statut=<?= $o['id'] ?>&statut=atteint&onglet=objectifs" class="btn-sm">✅ Atteint</a>
                    <a href="?changer_statut=<?= $o['id'] ?>&statut=abandonne&onglet=objectifs" class="btn-sm">⛔ Abandonné</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>