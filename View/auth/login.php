<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        .recover {
            text-align: right;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .recover a {
            text-decoration: none;
            color: rgb(125, 235, 131);
        }
        .recover a:hover {
            color: green;
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
        .icons {
            text-align: center;
        }
        .icons i {
            color: green;
            padding: 0.8rem 1.5rem;
            border-radius: 9px;
            font-size: 1rem;
            cursor: pointer;
            border: 2px solid #dfe9f5;
            margin: 0 15px;
            transition: 1s;
        }
        .icons i:hover {
            background: #07001f;
            font-size: 1.6rem;
            border: 2px solid green;
        }
        .links {
            display: flex;
            justify-content: space-around;
            padding: 0 4rem;
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
    </style>
</head>
<body>

<div class="container">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo">
    <h1 class="form-title">🌿 Connexion</h1>

    <div class="form">

        <?php if ($error): ?>
            <div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> Inscription réussie ! Vous pouvez maintenant vous connecter.</div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="email" id="email" placeholder="Email" required>
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Mot de passe" required>
                <label for="password">Mot de passe</label>
            </div>

            <div class="recover">
                <a href="#">Mot de passe oublié ?</a>
            </div>

            <input type="submit" class="btn" value="Se connecter">
        </form>

        <p class="or">------ ou ------</p>
        <div class="icons">
            <i class="fab fa-google"></i>
            <i class="fab fa-facebook"></i>
        </div>

        <div class="links">
            <p>Pas de compte ?</p>
            <button onclick="window.location.href='register.php'">S'inscrire</button>
        </div>

    </div>
</div>

</body>
</html>