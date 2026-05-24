<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('admin');

$message = '';
$erreur = '';

// Récupérer la liste des filières pour le filtre
$filieres = $pdo->query("SELECT id, nom FROM filieres ORDER BY nom")->fetchAll();
$filiere_id = $_GET['filiere'] ?? ($filieres[0]['id'] ?? 0);

// Ajouter une question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajouter'])) {
    $filiere_id_post = (int)$_POST['filiere_id'];
    $question = nettoyer($_POST['question']);
    $option_a = nettoyer($_POST['option_a']);
    $option_b = nettoyer($_POST['option_b']);
    $option_c = nettoyer($_POST['option_c']);
    $option_d = nettoyer($_POST['option_d']);
    $bonne_reponse = $_POST['bonne_reponse'];
    
    if (empty($question)) {
        $erreur = "La question est requise.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO questionnaires (filiere_id, question, option_a, option_b, option_c, option_d, bonne_reponse) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$filiere_id_post, $question, $option_a, $option_b, $option_c, $option_d, $bonne_reponse]);
        $message = "Question ajoutée.";
        header("refresh:2;url=questionnaires.php?filiere=" . $filiere_id_post);
    }
}

// Supprimer une question
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM questionnaires WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: questionnaires.php?filiere=' . $filiere_id);
    exit;
}

// Récupérer les questions de la filière sélectionnée
$questions = [];
if ($filiere_id) {
    $stmt = $pdo->prepare("SELECT * FROM questionnaires WHERE filiere_id = ? ORDER BY id");
    $stmt->execute([$filiere_id]);
    $questions = $stmt->fetchAll();
}

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>📝 Gestion des questionnaires</h1>

<?php if ($message): ?><div class="alert alert-success"><?= $message ?></div><?php endif; ?>
<?php if ($erreur): ?><div class="alert alert-error"><?= $erreur ?></div><?php endif; ?>

<div style="display:flex; gap:1rem; margin-bottom:1rem;">
    <label>Sélectionner une filière :</label>
    <form method="GET" style="display:inline;">
        <select name="filiere" onchange="this.form.submit()">
            <?php foreach ($filieres as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $filiere_id == $f['id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['nom']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div style="display:grid; grid-template-columns:1fr 2fr; gap:2rem;">
    <!-- Formulaire d'ajout -->
    <div class="card">
        <h3>➕ Ajouter une question</h3>
        <form method="POST">
            <input type="hidden" name="filiere_id" value="<?= $filiere_id ?>">
            <div class="form-group"><label>Question</label><textarea name="question" rows="2" required></textarea></div>
            <div class="form-group"><label>Option A</label><input type="text" name="option_a" required></div>
            <div class="form-group"><label>Option B</label><input type="text" name="option_b" required></div>
            <div class="form-group"><label>Option C</label><input type="text" name="option_c" required></div>
            <div class="form-group"><label>Option D</label><input type="text" name="option_d" required></div>
            <div class="form-group"><label>Bonne réponse</label>
                <select name="bonne_reponse">
                    <option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                </select>
            </div>
            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
        </form>
    </div>

    <!-- Liste des questions -->
    <div class="card">
        <h3>📋 Questions (<?= count($questions) ?>)</h3>
        <?php if (empty($questions)): ?>
            <p>Aucune question pour cette filière.</p>
        <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Question</th><th>Bonne réponse</th><th>Action</th></tr></thead>
                <tbody>
                    <?php foreach ($questions as $q): ?>
                    <tr>
                        <td><?= htmlspecialchars($q['question']) ?></td>
                        <td><?= strtoupper($q['bonne_reponse']) ?></td>
                        <td><a href="?delete=<?= $q['id'] ?>&filiere=<?= $filiere_id ?>" onclick="return confirm('Supprimer ?')" class="btn-danger">🗑️</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

