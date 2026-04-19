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
    
    // Validation
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
        if ($ctrl->register($nom, $prenom, $email, $password)) {
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
        
        <div class="auth-links">
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </div>
    </div>
</div>

<script src="../assets/validation.js"></script>
</body>
</html>