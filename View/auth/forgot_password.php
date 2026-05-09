<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$error = '';
$resetLink = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ctrl = new ControlUser();
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = "Veuillez entrer votre email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    } else {
        if ($ctrl->generateAndSendResetToken($email)) {
            $message = "Un email de réinitialisation a été généré.";
            // The reset link will be displayed by the sendMail function
        } else {
            $message = "Si cet email existe dans notre base, vous recevrez un lien de réinitialisation.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: linear-gradient(to right, white, olive); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .reset-card {
            background: white;
            width: 500px;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        .reset-card h1 { color: olivedrab; margin-bottom: 10px; }
        .reset-card p { color: #666; margin-bottom: 25px; }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
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
        .back-link { display: block; margin-top: 20px; color: olivedrab; text-decoration: none; }
        .alert-error { background: #ffe0e0; color: #cc0000; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background: #e0ffe0; color: #006600; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="reset-card">
        <i class="fas fa-key" style="font-size: 3rem; color: olivedrab;"></i>
        <h1>Mot de passe oublié</h1>
        <p>Entrez votre email pour recevoir un lien de réinitialisation</p>
        
        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="alert-success"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Votre email" required>
            <button type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </form>
        
        <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
    </div>
</body>
</html>