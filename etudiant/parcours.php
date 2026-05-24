<?php
// etudiant/parcours.php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('etudiant');

$user_id = $_SESSION['user_id'];
$erreur = '';
$success = '';

// Ajouter une UE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $niveau = $_POST['niveau'];
    $nom_ue = nettoyer($_POST['nom_ue']);
    $coeff = floatval($_POST['coefficient']);
    $note = floatval($_POST['note']);
    $annee = !empty($_POST['annee_academique']) ? (int)$_POST['annee_academique'] : null;
    
    if ($coeff <= 0) {
        $erreur = "Le coefficient doit être supérieur à 0.";
    } elseif ($note < 0 || $note > 20) {
        $erreur = "La note doit être coumprise entre 0 et 20.";
    } elseif (empty($nom_ue)) {
        $erreur = "Le nom de l'UE est requis.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO notes_etudiant (etudiant_id, niveau, nom_ue, coefficient, note, annee_academique) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$user_id, $niveau, $nom_ue, $coeff, $note, $annee]);
        $success = "UE ajoutée avec succès.";
        // Recalcul de la moyenne générale
        calculerMoyenneGenerale($pdo, $user_id);
        // Redirection pour éviter la resoumission
        header("Location: parcours.php");
        exit;
    }
}

// Supprimer une UE
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM notes_etudiant WHERE id = ? AND etudiant_id = ?");
    $stmt->execute([$id, $user_id]);
    calculerMoyenneGenerale($pdo, $user_id);
    header("Location: parcours.php");
    exit;
}

// Récupérer toutes les notes de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM notes_etudiant WHERE etudiant_id = ? ORDER BY FIELD(niveau, 'L1','L2','L3','M1','M2'), id");
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll();

// Calcul des moyennes par niveau et générale
$moyennes_niveau = [];
$total_coeff = 0;
$total_note_coeff = 0;
foreach ($notes as $n) {
    $niv = $n['niveau'];
    if (!isset($moyennes_niveau[$niv])) {
        $moyennes_niveau[$niv] = ['coeff_sum' => 0, 'note_coeff_sum' => 0];
    }
    $moyennes_niveau[$niv]['coeff_sum'] += $n['coefficient'];
    $moyennes_niveau[$niv]['note_coeff_sum'] += $n['note'] * $n['coefficient'];
    $total_coeff += $n['coefficient'];
    $total_note_coeff += $n['note'] * $n['coefficient'];
}
$moyenne_generale = ($total_coeff > 0) ? $total_note_coeff / $total_coeff : 0;

require_once dirname(__DIR__) . '/includes/header.php';
?>

<h1>📚 Mon parcours académique (Unités d'enseignement)</h1>
<p>Saisissez vos notes par UE pour que votre moyenne générale soit calculée automatiquement.</p>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>
<?php if ($erreur): ?>
    <div class="alert alert-error">❌ <?= $erreur ?></div>
<?php endif; ?>

<!-- Formulaire d'ajout d'UE -->
<div style="background:white; padding:1rem; border-radius:1rem; margin-bottom:2rem;">
    <h3>➕ Ajouter une unité d'enseignement</h3>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Niveau</label>
                <select name="niveau" required>
                    <option value="L1">L1</option>
                    <option value="L2">L2</option>
                    <option value="L3">L3</option>
                    <option value="M1">M1</option>
                    <option value="M2">M2</option>
                </select>
            </div>
            <div class="form-group">
                <label>Nom de l'UE</label>
                <input type="text" name="nom_ue" placeholder="Ex: Programmation Web" required>
            </div>
            <div class="form-group">
                <label>Coefficient</label>
                <input type="number" step="0.01" name="coefficient" placeholder="Ex: 3" required>
            </div>
            <div class="form-group">
                <label>Note /20</label>
                <input type="number" step="0.01" name="note" placeholder="Ex: 14.5" required>
            </div>
            <div class="form-group">
                <label>Année (optionnel)</label>
                <input type="number" step="1" name="annee_academique" placeholder="Ex: 2025">
            </div>
        </div>
        <button type="submit" name="ajouter" class="btn btn-primary">Ajouter l'UE</button>
    </form>
</div>

<!-- Liste des UE saisies -->
<?php if (empty($notes)): ?>
    <div class="alert alert-info">📭 Vous n'avez encore saisi aucune unité d'enseignement.</div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Niveau</th>
                <th>UE</th>
                <th>Coeff.</th>
                <th>Note</th>
                <th>Année</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $n): ?>
            <tr>
                <td><?= $n['niveau'] ?></td>
                <td><?= htmlspecialchars($n['nom_ue']) ?></td>
                <td><?= $n['coefficient'] ?></td>
                <td><?= $n['note'] ?></td>
                <td><?= $n['annee_academique'] ?? '-' ?></td>
                <td><a href="?supprimer=<?= $n['id'] ?>" onclick="return confirm('Supprimer cette UE ?')" class="btn-danger" style="padding:0.2rem 0.5rem;">🗑️ Supprimer</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>📊 Résumé des moyennes</h3>
    <ul>
        <?php foreach ($moyennes_niveau as $niv => $data): 
            $moy = $data['coeff_sum'] > 0 ? $data['note_coeff_sum'] / $data['coeff_sum'] : 0;
        ?>
            <li><strong><?= $niv ?> :</strong> <?= round($moy,2) ?> /20 (sur <?= $data['coeff_sum'] ?> crédits)</li>
        <?php endforeach; ?>
    </ul>
    <p><strong>📈 Moyenne générale : <?= round($moyenne_generale,2) ?> /20</strong></p>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>