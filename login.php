<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (estConnecte()) { 
    redirigerSelonRole(); 
    exit; 
}

$erreur = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    
    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Veuillez remplir tous les champs";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            redirigerSelonRole();
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect";
            // Journalisation de la tentative échouée (optionnel)
            error_log("Tentative de connexion échouée pour: " . $email);
        }
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="form-container">
    <div class="form-card">
        <div class="form-header">
            <div class="form-icon">🔐</div>
            <h2>Connexion</h2>
            <p>Accédez à votre espace personnel</p>
        </div>
        <div class="form-body">
            <?php if ($erreur): ?>
                <div class="alert alert-error">
                    <span>❌</span>
                    <div><?= $erreur ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>📧 Adresse email</label>
                    <input type="email" 
                           name="email" 
                           value="<?= htmlspecialchars($email) ?>" 
                           placeholder="exemple@email.com" 
                           required 
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label>🔒 Mot de passe</label>
                    <input type="password" 
                           name="mot_de_passe" 
                           placeholder="Votre mot de passe" 
                           required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    🔓 Se connecter
                </button>
                
                <div class="form-footer">
                    <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
                    <p><a href="<?= BASE_URL ?>">🏠 Retour à l'accueil</a></p>
                </div>
            </form>
            
            <!-- SECTION DES COMPTES DE DÉMONSTRATION - COMMENTÉE / MASQUÉE -->
            <?php if (false): // Mettre à false pour cacher, à true pour afficher ?>
            <div class="demo-accounts">
                <h4>📋 Comptes de démonstration</h4>
                <div class="demo-item">
                    <span class="demo-role">👨‍🎓 Étudiant</span>
                    <code>marie@etudiant.cm</code>
                    <span class="demo-pwd">admin123</span>
                </div>
                <div class="demo-item">
                    <span class="demo-role">👔 Conseiller</span>
                    <code>conseiller@orientation.com</code>
                    <span class="demo-pwd">admin123</span>
                </div>
                <div class="demo-item">
                    <span class="demo-role">👑 Administrateur</span>
                    <code>admin@orientation.cm</code>
                    <span class="demo-pwd">admin123</span>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>