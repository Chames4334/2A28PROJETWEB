<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nettoyer le lien stocké
if (isset($_SESSION['reset_link'])) {
    unset($_SESSION['reset_link']);
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new ControlUser();
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 8) {
        $error = "Minimum 8 caractères";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Au moins une majuscule";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Au moins un chiffre";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        if ($ctrl->resetPassword($token, $password)) {
            header('Location: login.php?reset=1');
            exit;
        } else {
            $error = "Lien invalide ou expiré";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(to right, white, olive); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .reset-card {
            background: white;
            width: 450px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        .reset-card h1 { color: olivedrab; margin-bottom: 20px; }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 1rem;
        }
        button {
            width: 100%;
            padding: 12px;
            background: olivedrab;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover { background: #5a7a26; }
        .alert-error { background: #ffe0e0; color: #cc0000; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="reset-card">
        <i class="fas fa-lock" style="font-size: 3rem; color: olivedrab;"></i>
        <h1>Nouveau mot de passe</h1>
        
        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmer" required>
            <button type="submit"><i class="fas fa-save"></i> Réinitialiser</button>
        </form>
    </div>
</body>
</html>