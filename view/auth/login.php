<?php
// view/auth/login.php
include_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in - redirect to forum
if (isset($_SESSION['user_id'])) {
    header('Location: ../frontoffice/forum/liste.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    // Server-side validation
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        // Check credentials against users table
        try {
            $db = config::getConnexion();
            $stmt = $db->prepare("
                SELECT u.id, u.nom, u.prenom, u.email, u.password_hash, u.status,
                       GROUP_CONCAT(r.nom) as roles
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = :email AND u.status = 'active'
                GROUP BY u.id
                LIMIT 1
            ");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Check if user has admin role
                $userRoles = explode(',', $user['roles']);
                $isAdmin = in_array('admin', $userRoles);

                // Login successful - set session
                $_SESSION['user_id']     = $user['id'];
                $_SESSION['user_nom']    = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email']  = $user['email'];
                $_SESSION['user_role']   = $isAdmin ? 'admin' : 'user';

                header('Location: ../frontoffice/forum/liste.php');
                exit;
            } else {
                $error = 'Email ou mot de passe incorrect.';
            }
        } catch (Exception $e) {
            $error = 'Erreur lors de la connexion : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/forum.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 100%);
        }
        .auth-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 420px;
        }
        .auth-card h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: var(--green-dark);
            text-align: center;
            margin-bottom: 8px;
        }
        .auth-card p {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 28px;
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--text-mid);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--green-pale);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.95rem;
            transition: 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--green-main);
            background: var(--white);
        }
        .auth-btn {
            width: 100%;
            padding: 12px 14px;
            background: var(--green-main);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 8px;
        }
        .auth-btn:hover {
            background: var(--green-dark);
        }
        .alert {
            padding: 12px 14px;
            background: #fce4ec;
            color: #c62828;
            border: 1px solid #f48fb1;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-light);
        }
    </style>
</head>
<body>

<div class="auth-card">
    <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
    <p>Accédez à votre compte Green Assurance</p>

    <?php if ($error): ?>
        <div class="alert">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" id="loginForm" novalidate>
        <div class="form-group">
            <label for="email"><i class="fas fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email) ?>"
                   placeholder="votre@email.com">
        </div>

        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
            <input type="password" id="password" name="password"
                   placeholder="••••••••">
        </div>

        <button type="submit" class="auth-btn">
            <i class="fas fa-sign-in-alt"></i> Se connecter
        </button>
    </form>

    <div class="auth-footer">
        <p>Note: Utilisez vos identifiants existants du système.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();
        
        if (!email || !password) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs.');
        } else if (!email.includes('@')) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
        } else if (password.length < 3) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 3 caractères.');
        }
    });
});
</script>

</body>
</html>
