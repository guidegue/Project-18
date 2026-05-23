<?php
// Configuration pour le dossier "projet18"
define('BASE_URL', 'http://localhost/projet18/');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'orientat_db');
define('APP_NAME', 'OrientPro');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur BDD: " . $e->getMessage());
}
?>