<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si déjà connecté, rediriger
if (isset($_SESSION['user_id'])) {
    header('Location: ../backoffice/liste.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new ControlUser();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($ctrl->login($email, $password)) {
        header('Location: ../backoffice/liste.php');
        exit;
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
           <img src="../assets/logo.png" alt="Green Assurance" class="logo-img">
            <h1>🌿 Green Assurance</h1>
            <p>Assurance verte, avenir serein</p>
        </div>
        
        <h2><i class="fas fa-sign-in-alt"></i> Connexion</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" id="loginForm" novalidate>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="text" name="email" id="email">
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" name="password" id="password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
        
        <div class="auth-links">
            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>
</div>

<script src="../assets/validation.js"></script>
</body>
</html>