<?php
// =============================================
// functions.php - Fonctions utilitaires
// =============================================

function estConnecte() {
    return isset($_SESSION['user_id']);
}

function aRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireLogin() {
    if (!estConnecte()) {
        header('Location: ' . BASE_URL . 'auth/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!aRole($role)) {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

function redirigerSelonRole() {
    if (!estConnecte()) {
        header('Location: ' . BASE_URL . 'auth/login.php');
        exit;
    }
    
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: ' . BASE_URL . 'admin/dashboard.php');
            break;
        case 'conseiller':
            header('Location: ' . BASE_URL . 'conseiller/dashboard.php');
            break;
        default:
            header('Location: ' . BASE_URL . 'etudiant/dashboard.php');
    }
    exit;
}

function nettoyer($data) {
    if ($data === null) return '';
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function hacherMotDePasse($mdp) {
    return password_hash($mdp, PASSWORD_DEFAULT);
}

function verifierMotDePasse($mdp, $hash) {
    return password_verify($mdp, $hash);
}

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}


function calculerMoyenneGenerale($pdo, $etudiant_id) {
    $stmt = $pdo->prepare("
        SELECT SUM(coefficient * note) / SUM(coefficient) as moyenne
        FROM notes_etudiant
        WHERE etudiant_id = ?
    ");
    $stmt->execute([$etudiant_id]);
    $moyenne = $stmt->fetchColumn();
    $moyenne = $moyenne ? round($moyenne, 2) : null;
    
    // Mise à jour dans la table profils_etudiants
    $stmt = $pdo->prepare("UPDATE profils_etudiants SET moyenne_generale = ? WHERE etudiant_id = ?");
    $stmt->execute([$moyenne, $etudiant_id]);
    return $moyenne;
}

?>



