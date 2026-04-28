<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../backoffice/liste.php');
    exit;
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new ControlUser();
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (empty($nom)) $errors['nom'] = "Nom obligatoire";
    elseif (strlen($nom) < 2) $errors['nom'] = "Minimum 2 caractères";
    
    if (empty($prenom)) $errors['prenom'] = "Prénom obligatoire";
    elseif (strlen($prenom) < 2) $errors['prenom'] = "Minimum 2 caractères";
    
    if (empty($email)) $errors['email'] = "Email obligatoire";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide";
    elseif ($ctrl->emailExists($email)) $errors['email'] = "Email déjà utilisé";
    
    if (empty($password)) $errors['password'] = "Mot de passe obligatoire";
    elseif (strlen($password) < 8) $errors['password'] = "Minimum 8 caractères";
    elseif (!preg_match('/[A-Z]/', $password)) $errors['password'] = "Au moins une majuscule";
    elseif (!preg_match('/[0-9]/', $password)) $errors['password'] = "Au moins un chiffre";
    
    if ($password !== $confirm) $errors['confirm_password'] = "Les mots de passe ne correspondent pas";
    
    if (empty($errors)) {
        if ($ctrl->registerWithVerification($nom, $prenom, $email, $password)) {
            header('Location: login.php?registered=1');
            exit;
        }
    } else {
        $old = ['nom' => $nom, 'prenom' => $prenom, 'email' => $email];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .navbar {
            background: rgba(255,255,255,0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar .logo-nav { display: flex; align-items: center; gap: 10px; }
        .navbar .logo-nav img { height: 40px; }
        .navbar .logo-nav h1 { font-size: 1.3rem; color: olivedrab; }
        .navbar .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 25px;
            font-weight: 500;
            transition: 0.3s;
        }
        .navbar .nav-links a:hover { color: olivedrab; }
        .navbar .nav-links .btn-accueil {
            background: olivedrab;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
        }
        .navbar .nav-links .btn-connexion {
            background: transparent;
            border: 2px solid olivedrab;
            color: olivedrab;
            padding: 8px 20px;
            border-radius: 25px;
        }
        .info-email {
            text-align: center;
            margin-top: 15px;
            font-size: 0.8rem;
            color: #666;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo-nav">
        <img src="../assets/logo.png" alt="Green Assurance">
        <h1>🌿 Green Assurance</h1>
    </div>
    <div class="nav-links">
        <a href="../frontoffice/accueil.php" class="btn-accueil"><i class="fas fa-home"></i> Accueil</a>
        <a href="login.php" class="btn-connexion"><i class="fas fa-sign-in-alt"></i> Connexion</a>
    </div>
</div>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="../assets/logo.png" alt="Green Assurance" class="logo-img">
            <h1>🌿 Green Assurance</h1>
            <p>Rejoignez l'aventure verte</p>
        </div>
        
        <h2><i class="fas fa-user-plus"></i> Inscription</h2>
        
        <form method="POST" action="" id="registerForm" novalidate>
            <div class="form-row">
                <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-user"></i> Nom</label>
                    <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>">
                    <?php if(isset($errors['nom'])): ?><span class="error-msg"><?= $errors['nom'] ?></span><?php endif; ?>
                </div>
                
                <div class="form-group <?= isset($errors['prenom']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-user"></i> Prénom</label>
                    <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>">
                    <?php if(isset($errors['prenom'])): ?><span class="error-msg"><?= $errors['prenom'] ?></span><?php endif; ?>
                </div>
            </div>
            
            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="text" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <?php if(isset($errors['email'])): ?><span class="error-msg"><?= $errors['email'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="password" id="password">
                    <small>8 caractères min, 1 majuscule, 1 chiffre</small>
                    <?php if(isset($errors['password'])): ?><span class="error-msg"><?= $errors['password'] ?></span><?php endif; ?>
                </div>
                
                <div class="form-group <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                    <label><i class="fas fa-lock"></i> Confirmer</label>
                    <input type="password" name="confirm_password" id="confirm_password">
                    <?php if(isset($errors['confirm_password'])): ?><span class="error-msg"><?= $errors['confirm_password'] ?></span><?php endif; ?>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> S'inscrire
            </button>
        </form>
        
        <div class="info-email">
            <i class="fas fa-envelope"></i> Un email de vérification vous sera envoyé
        </div>
        
        <div class="auth-links">
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

<script src="../assets/validation.js"></script>
</body>
</html>