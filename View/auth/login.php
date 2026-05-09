<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: ../backoffice/liste.php');
    } else {
        header('Location: ../frontoffice/accueil.php');
    }
    exit;
}

$error = '';
$captcha_question = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new ControlUser();
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    
    if (!$ctrl->verifyCaptcha($captcha)) {
        $error = "Code CAPTCHA incorrect";
    } else {
        $result = $ctrl->loginWithAttempts($email, $password);
        if ($result['success']) {
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                header('Location: ../backoffice/liste.php');
            } else {
                header('Location: ../frontoffice/accueil.php');
            }
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

$ctrl = new ControlUser();
$captcha_question = $ctrl->generateCaptcha();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Green Assurance</title>
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
        .container {
            background: white;
            width: 450px;
            padding: 1.5rem;
            margin: 50px auto;
            border-radius: 10px;
            box-shadow: 0 20px 35px rgba(0,0,1,0.9);
        }
        .form {
            margin: 0 2rem;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            padding: 1.3rem;
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
        .captcha-box {
            background: #f0f0f0;
            padding: 10px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .recover {
            text-align: right;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .recover a {
            text-decoration: none;
            color: olivedrab;
        }
        .recover a:hover {
            text-decoration: underline;
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
        .or {
            font-size: 1.1rem;
            margin-top: 0.5rem;
            text-align: center;
        }
        .links {
            display: flex;
            justify-content: space-around;
            padding: 0 2rem;
            margin-top: 0.9rem;
            font-weight: bold;
        }
        button {
            color: green;
            border: none;
            background-color: transparent;
            font-size: 1rem;
            font-weight: bold;
        }
        button:hover {
            text-decoration: underline;
            color: green;
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
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            padding: 0.7rem 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 0.7rem 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
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
        <a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a>
        <a href="forgot_password.php"><i class="fas fa-key"></i> Mot de passe oublié</a>
    </div>
</div>

<div class="container">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo">
    <h1 class="form-title">🌿 Connexion</h1>

    <div class="form">

        <?php if ($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> Inscription réussie ! Un email de vérification vous a été envoyé.</div>
        <?php endif; ?>

        <?php if (isset($_GET['verified'])): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> Email vérifié ! Vous pouvez maintenant vous connecter.</div>
        <?php endif; ?>

        <?php if (isset($_GET['reset'])): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> Mot de passe réinitialisé ! Connectez-vous.</div>
        <?php endif; ?>

        <?php if (isset($_GET['deactivated'])): ?>
            <div class="alert-warning"><i class="fas fa-info-circle"></i> Votre compte a été désactivé. Contactez l'administrateur pour le réactiver.</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert-info"><i class="fas fa-info-circle"></i> Votre compte a été supprimé. Merci d'avoir utilisé Green Assurance.</div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Mot de passe" required>
                <label for="password">Mot de passe</label>
            </div>

            <div class="captcha-box">
                <?= $captcha_question ?>
            </div>
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="captcha" id="captcha" placeholder="Résultat" required>
                <label for="captcha">Résultat du calcul</label>
            </div>

            <div class="recover">
                <a href="forgot_password.php">Mot de passe oublié ?</a>
            </div>

            <input type="submit" class="btn" value="Se connecter">
        </form>

        <!-- BOUTON GOOGLE AJOUTÉ ICI -->
        <?php if (file_exists('google-login.php')): ?>
            <?php include 'google-login.php'; ?>
        <?php endif; ?>

        <p class="or">------ ou ------</p>

        <div class="links">
            <p>Pas de compte ?</p>
            <button onclick="window.location.href='register.php'">S'inscrire</button>
        </div>

    </div>
</div>

</body>
</html>