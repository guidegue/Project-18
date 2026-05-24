<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
requireRole('conseiller');

$sql = "
    SELECT u.id, u.nom, u.prenom, u.email,
           p.filiere_id, f.nom as filiere_nom,
           p.moyenne_generale,
           (SELECT COUNT(*) FROM reponses_etudiants WHERE etudiant_id = u.id) as nb_reponses,
           (SELECT COUNT(*) FROM questionnaires q WHERE q.filiere_id = p.filiere_id) as total_questions
    FROM utilisateurs u
    LEFT JOIN profils_etudiants p ON u.id = p.etudiant_id
    LEFT JOIN filieres f ON p.filiere_id = f.id
    WHERE u.role = 'etudiant'
    ORDER BY u.date_inscription DESC
";
$etudiants = $pdo->query($sql)->fetchAll();

require_once dirname(__DIR__) . '/includes/header.php';
?>
<h1>👥 Liste des étudiants</h1>
<table class="data-table">
    <thead>
        <tr><th>Nom</th><th>Email</th><th>Filière</th><th>Moyenne</th><th>Progression</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php foreach ($etudiants as $e): 
            $progression = ($e['total_questions'] > 0) ? round(($e['nb_reponses'] / $e['total_questions']) * 100) : 0;
        ?>
        <tr>
            <td><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><?= htmlspecialchars($e['filiere_nom'] ?? 'Non définie') ?></td>
            <td><?= $e['moyenne_generale'] ? $e['moyenne_generale'] . '/20' : '-' ?></td>
            <td><?= $progression ?>% (<?= $e['nb_reponses'] ?>/<?= $e['total_questions'] ?>)</td>
            <td>
                <a href="voir_etudiant.php?id=<?= $e['id'] ?>" class="btn-sm">👁️ Voir</a>
                <a href="orienter_etudiant.php?id=<?= $e['id'] ?>" class="btn-sm">🎯 Orienter</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

