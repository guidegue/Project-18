<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$erreur = '';
$success = '';

// Vérifier si on est en mode édition (paramètre edit=1 dans l'URL)
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 1;

// Récupérer toutes les filières pour le select
$filieres = $pdo->query('SELECT id, nom FROM filieres ORDER BY nom')->fetchAll();

// Récupérer le profil existant
$stmt = $pdo->prepare('SELECT * FROM profils_etudiants WHERE etudiant_id = ?');
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

// Traitement du formulaire d'enregistrement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    $matricule = nettoyer($_POST['matricule']);
    $date_naissance = $_POST['date_naissance'] ?: null;
    $telephone = nettoyer($_POST['telephone']);
    $adresse = nettoyer($_POST['adresse']);
    $annee_bac = $_POST['annee_bac'] ?: null;
    $filiere_id = $_POST['filiere_id'] ?: null;
    $niveau = $_POST['niveau'];
    $interets = nettoyer($_POST['interets']);
    
    try {
        if ($profil) {
            // Mise à jour
            $sql = "UPDATE profils_etudiants SET 
                        matricule=?, date_naissance=?, telephone=?, adresse=?, annee_bac=?,
                        filiere_id=?, niveau=?, interets=?
                    WHERE etudiant_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$matricule, $date_naissance, $telephone, $adresse, $annee_bac,
                            $filiere_id, $niveau, $interets, $user_id]);
        } else {
            // Insertion
            $sql = "INSERT INTO profils_etudiants 
                        (etudiant_id, matricule, date_naissance, telephone, adresse, annee_bac,
                         filiere_id, niveau, interets)
                    VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $matricule, $date_naissance, $telephone, $adresse, $annee_bac,
                            $filiere_id, $niveau, $interets]);
        }
        // Redirection sans paramètre edit, en mode consultation
        header('Location: ' . BASE_URL . 'etudiant/profil.php?success=1');
        exit;
    } catch (PDOException $e) {
        $erreur = "Erreur BDD : " . $e->getMessage();
    }
}

// Message de succès après enregistrement
if (isset($_GET['success'])) {
    $success = "✅ Profil enregistré avec succès !";
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <h2>👤 Mon profil académique</h2>
            <p>Consultez et modifiez vos informations personnelles</p>
        </div>
        <div class="form-body">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if ($erreur): ?>
                <div class="alert alert-error">❌ <?= $erreur ?></div>
            <?php endif; ?>

            <?php if ($profil && !$edit_mode): ?>
                <!-- MODE CONSULTATION : affichage des informations en lecture seule -->
                <div class="profile-info">
                    <div class="info-row"><strong>Matricule :</strong> <?= htmlspecialchars($profil['matricule'] ?? 'Non renseigné') ?></div>
                    <div class="info-row"><strong>Date de naissance :</strong> <?= $profil['date_naissance'] ? date('d/m/Y', strtotime($profil['date_naissance'])) : 'Non renseignée' ?></div>
                    <div class="info-row"><strong>Téléphone :</strong> <?= htmlspecialchars($profil['telephone'] ?? 'Non renseigné') ?></div>
                    <div class="info-row"><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($profil['adresse'] ?? 'Non renseignée')) ?></div>
                    <div class="info-row"><strong>Année bac :</strong> <?= $profil['annee_bac'] ?? 'Non renseignée' ?></div>
                    <div class="info-row"><strong>Filière :</strong> 
                        <?php 
                        $filiere_nom = '';
                        foreach ($filieres as $f) {
                            if ($f['id'] == $profil['filiere_id']) $filiere_nom = $f['nom'];
                        }
                        echo htmlspecialchars($filiere_nom ?: 'Non définie');
                        ?>
                    </div>
                    <div class="info-row"><strong>Niveau :</strong> <?= $profil['niveau'] ?? 'Non renseigné' ?></div>
                    <div class="info-row"><strong>Centres d'intérêt :</strong> <?= nl2br(htmlspecialchars($profil['interets'] ?? 'Non renseignés')) ?></div>
                </div>
                <div style="margin-top: 1.5rem; text-align: center;">
                    <a href="?edit=1" class="btn btn-primary">✏️ Modifier mon profil</a>
                </div>

            <?php else: ?>
                <!-- MODE ÉDITION : formulaire de saisie / modification -->
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Matricule / N° étudiant *</label>
                            <input type="text" name="matricule" value="<?= $profil ? htmlspecialchars($profil['matricule']) : '' ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Date de naissance</label>
                            <input type="date" name="date_naissance" value="<?= $profil ? $profil['date_naissance'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" name="telephone" value="<?= $profil ? htmlspecialchars($profil['telephone']) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Année du baccalauréat</label>
                            <input type="number" name="annee_bac" min="2000" max="2030" step="1" value="<?= $profil ? $profil['annee_bac'] : '' ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <textarea name="adresse" rows="2"><?= $profil ? htmlspecialchars($profil['adresse']) : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Filière *</label>
                        <select name="filiere_id" required>
                            <option value="">-- Sélectionnez --</option>
                            <?php foreach ($filieres as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= ($profil && $profil['filiere_id'] == $f['id']) ? 'selected' : '' ?>><?= htmlspecialchars($f['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Niveau</label>
                        <select name="niveau" required>
                            <option value="L1" <?= ($profil && $profil['niveau'] == 'L1') ? 'selected' : '' ?>>L1</option>
                            <option value="L2" <?= ($profil && $profil['niveau'] == 'L2') ? 'selected' : '' ?>>L2</option>
                            <option value="L3" <?= ($profil && $profil['niveau'] == 'L3') ? 'selected' : '' ?>>L3</option>
                            <option value="M1" <?= ($profil && $profil['niveau'] == 'M1') ? 'selected' : '' ?>>M1</option>
                            <option value="M2" <?= ($profil && $profil['niveau'] == 'M2') ? 'selected' : '' ?>>M2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Centres d'intérêt</label>
                        <textarea name="interets" rows="3" placeholder="Ex: Développement web, IA, Cybersécurité..."><?= $profil ? htmlspecialchars($profil['interets']) : '' ?></textarea>
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: space-between;">
                        <button type="submit" name="save" class="btn btn-primary">💾 Enregistrer</button>
                        <?php if ($profil): ?>
                            <a href="profil.php" class="btn btn-outline">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.profile-info .info-row {
    margin-bottom: 0.8rem;
    padding: 0.5rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}
.profile-info strong {
    display: inline-block;
    width: 150px;
    color: #374151;
}
.btn-outline {
    background: transparent;
    border: 1px solid #059669;
    color: #059669;
    padding: 0.6rem 1rem;
    border-radius: 0.5rem;
    text-decoration: none;
    text-align: center;
    display: inline-block;
}
.btn-outline:hover {
    background: #059669;
    color: white;
}
</style>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>