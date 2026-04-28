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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            background-color: olive;
            background: linear-gradient(to right, white, olive);
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .logo-nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar .logo-nav img {
            height: 40px;
        }
        .navbar .logo-nav h1 {
            font-size: 1.3rem;
            color: olivedrab;
        }
        .navbar .nav-links a {
            text-decoration: none;
            color: #333;
            margin-left: 25px;
            font-weight: 500;
            transition: 0.3s;
        }
        .navbar .nav-links a:hover {
            color: olivedrab;
        }
        .navbar .nav-links .btn-accueil {
            background: olivedrab;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
        }
        .navbar .nav-links .btn-accueil:hover {
            background: #5a7a26;
            color: white;
        }
        .navbar .nav-links .btn-connexion {
            background: transparent;
            border: 2px solid olivedrab;
            color: olivedrab;
            padding: 8px 20px;
            border-radius: 25px;
        }
        .navbar .nav-links .btn-connexion:hover {
            background: olivedrab;
            color: white;
        }
        .container {
            background: white;
            width: 500px;
            padding: 1.5rem;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 20px 35px rgba(0,0,1,0.9);
        }
        .form {
            margin: 0 1.5rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 1rem;
            margin-bottom: 0.4rem;
        }
        .logo {
            display: block;
            margin: 0 auto 0.5rem;
            width: 80px;
        }
        input {
            color: inherit;
            width: 100%;
            background-color: transparent;
            border: none;
            border-bottom: 1px solid #757575;
            padding-left: 1.5rem;
            font-size: 15px;
        }
        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }
        .input-group i {
            position: absolute;
            color: black;
        }
        input:focus {
            background-color: transparent;
            outline: transparent;
            border-bottom: 2px solid #32CD32;
        }
        input::placeholder {
            color: transparent;
        }
        label {
            color: #757575;
            position: relative;
            left: 1.2rem;
            top: -1.3rem;
            cursor: auto;
            transition: 0.3s ease all;
        }
        input:focus ~ label,
        input:not(:placeholder-shown) ~ label {
            top: -3em;
            color: green;
            font-size: 15px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .btn {
            font-size: 1.1rem;
            padding: 8px 0;
            border-radius: 5px;
            outline: none;
            border: none;
            width: 100%;
            background: #90EE90;
            color: black;
            cursor: pointer;
            transition: 0.9s;
        }
        .btn:hover {
            background: #7CCD7C;
        }
        .alert-error {
            background: #ffe0e0;
            color: #cc0000;
            padding: 0.7rem 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-success {
            background: #e0ffe0;
            color: #006600;
            padding: 0.7rem 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .error-msg {
            color: #dc2626;
            font-size: 0.7rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .has-error input {
            border-bottom-color: #dc2626;
        }
        .small-note {
            font-size: 0.7rem;
            color: #888;
            text-align: center;
            margin-top: 15px;
        }
        .links {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 1.5rem;
            font-weight: bold;
        }
        .links a {
            text-decoration: none;
            color: olivedrab;
        }
        .links a:hover {
            text-decoration: underline;
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

<div class="container">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo">
    <h1 class="form-title">🌿 Inscription</h1>

    <div class="form">

        <?php if (isset($errors['general'])): ?>
            <div class="alert-error"><?= $errors['general'] ?></div>
        <?php endif; ?>

        <form method="POST" action="" id="registerForm" novalidate>
            <div class="form-row">
                <div class="input-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nom" id="nom" placeholder="Nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>">
                    <label for="nom">Nom</label>
                    <?php if(isset($errors['nom'])): ?><span class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $errors['nom'] ?></span><?php endif; ?>
                </div>

                <div class="input-group <?= isset($errors['prenom']) ? 'has-error' : '' ?>">
                    <i class="fas fa-user"></i>
                    <input type="text" name="prenom" id="prenom" placeholder="Prénom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>">
                    <label for="prenom">Prénom</label>
                    <?php if(isset($errors['prenom'])): ?><span class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $errors['prenom'] ?></span><?php endif; ?>
                </div>
            </div>

            <div class="input-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                <label for="email">Email</label>
                <?php if(isset($errors['email'])): ?><span class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $errors['email'] ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="input-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Mot de passe">
                    <label for="password">Mot de passe</label>
                    <?php if(isset($errors['password'])): ?><span class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $errors['password'] ?></span><?php endif; ?>
                </div>

                <div class="input-group <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmer">
                    <label for="confirm_password">Confirmer</label>
                    <?php if(isset($errors['confirm_password'])): ?><span class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= $errors['confirm_password'] ?></span><?php endif; ?>
                </div>
            </div>

            <div class="small-note">
                <i class="fas fa-info-circle"></i> 8 caractères min, 1 majuscule, 1 chiffre
            </div>

            <input type="submit" class="btn" value="S'inscrire">
        </form>

        <div class="small-note">
            <i class="fas fa-envelope"></i> Un email de vérification vous sera envoyé
        </div>

        <div class="links">
            <p>Déjà inscrit ?</p>
            <a href="login.php">Se connecter</a>
        </div>

    </div>
</div>

<script src="../assets/validation.js"></script>
</body>
</html>