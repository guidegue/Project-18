

<?php
if (!isset($pdo) && file_exists(dirname(__DIR__) . '/config.php')) {
    require_once dirname(__DIR__) . '/config.php';
}
if (!function_exists('estConnecte')) {
    require_once dirname(__DIR__) . '/includes/functions.php';
}

$nb_messages_non_lus = 0;
$notification_badge = '';

if (estConnecte()) {
    $user_id = $_SESSION['user_id'];
    try {
        // Utilisation de la colonne "lu" (tinyint) : 0 = non lu
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE destinataire_id = ? AND lu = 0");
        $stmt->execute([$user_id]);
        $nb_messages_non_lus = $stmt->fetchColumn();
        if ($nb_messages_non_lus > 0) {
            $notification_badge = '<span class="notification-badge">' . $nb_messages_non_lus . '</span>';
        }
    } catch (PDOException $e) {
        // Fallback silencieux
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Orientation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <style>
        /* Reset minimal */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f9fafb;
            color: #1f2937;
            line-height: 1.5;
        }
        .navbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top:0;
            z-index:1000;
        }
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .nav-brand a {
            font-size: 1.3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #059669, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .nav-link {
            padding: 0.5rem 1rem;
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .nav-link:hover {
            background: #ecfdf5;
            color: #059669;
        }
        .user-greeting {
            padding: 0.5rem 1rem;
            background: #ecfdf5;
            color: #059669;
            border-radius: 0.75rem;
            font-weight: 600;
        }
        .btn-nav {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white !important;
            padding: 0.5rem 1.2rem;
            border-radius: 0.75rem;
        }
        .btn-nav:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(5,150,105,0.3);
        }
        .notification-badge {
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 50%;
            margin-left: 0.3rem;
            display: inline-block;
        }
        .logout-link {
            color: #ef4444 !important;
        }
        .logout-link:hover {
            background: #fef2f2;
        }
        .container {
            max-width: 1280px;
            margin: 1.5rem auto;
            padding: 0 1.5rem;
        }
        .alert {
            padding: 0.8rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
        }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-info { background: #dbeafe; color: #1e40af; border-left: 4px solid #3b82f6; }
        @media (max-width: 768px) {
            .nav-container { flex-direction: column; align-items: stretch; text-align: center; }
            .nav-links { justify-content: center; }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="nav-brand">
            <a href="<?= BASE_URL ?>">🎓 OrientPro</a>
        </div>
        <div class="nav-links">
            <?php if (estConnecte()): ?>
                <span class="user-greeting">👋 <?= nettoyer($_SESSION['user_prenom'] ?? $_SESSION['user_nom'] ?? 'Utilisateur') ?></span>
                <?php if (aRole('etudiant')): ?>
                    <a href="<?= BASE_URL ?>etudiant/dashboard.php" class="nav-link">📊 Dashboard</a>
                    <a href="<?= BASE_URL ?>etudiant/profil.php" class="nav-link">👤 Profil</a>
                    <a href="<?= BASE_URL ?>etudiant/parcours.php" class="nav-link">📚 Parcours</a>
                    <a href="<?= BASE_URL ?>etudiant/questionnaire.php" class="nav-link">📝 Questionnaire</a>
                    <a href="<?= BASE_URL ?>etudiant/resultats.php" class="nav-link">📈 Résultats</a>
                    <a href="<?= BASE_URL ?>etudiant/ressources.php" class="nav-link">📖 Ressources</a>
                    <a href="<?= BASE_URL ?>etudiant/offres.php" class="nav-link">💼 Offres</a>
                    <a href="<?= BASE_URL ?>etudiant/messages.php" class="nav-link">💬 Messages <?= $notification_badge ?></a>
                    <a href="<?= BASE_URL ?>etudiant/progression.php" class="nav-link">📈progression <?= $notification_badge ?></a>

                <?php elseif (aRole('conseiller')): ?>
                    <a href="<?= BASE_URL ?>conseiller/dashboard.php" class="nav-link">📊 Dashboard</a>
                    <a href="<?= BASE_URL ?>conseiller/etudiants.php" class="nav-link">👥 Étudiants</a>
                    <a href="<?= BASE_URL ?>conseiller/offres.php" class="nav-link">💼 Offres</a>
                    <a href="<?= BASE_URL ?>conseiller/ressources.php" class="nav-link">📚 Ressources</a>
                    <a href="<?= BASE_URL ?>conseiller/messages.php" class="nav-link">💬 Messages <?= $notification_badge ?></a>
                <?php elseif (aRole('admin')): ?>
                    <a href="<?= BASE_URL ?>admin/dashboard.php" class="nav-link">⚙️ Admin</a>
                    <a href="<?= BASE_URL ?>admin/utilisateurs.php" class="nav-link">👥 Utilisateurs</a>
                    <a href="<?= BASE_URL ?>admin/filieres.php" class="nav-link">📚 Filières</a>
                    <a href="<?= BASE_URL ?>admin/messages.php" class="nav-link">💬 Messages <?= $notification_badge ?></a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>auth/logout.php" class="nav-link logout-link">🚪 Déconnexion</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>auth/login.php" class="nav-link btn-nav">🔐 Connexion</a>
                <a href="<?= BASE_URL ?>auth/register.php" class="nav-link btn-nav">✨ Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container">