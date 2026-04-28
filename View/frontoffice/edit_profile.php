<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$ctrl = new ControlUser();
$userId = $_SESSION['user_id'];
$user = $ctrl->getUserById($userId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? '')
    ];
    
    $errors = [];
    if (empty($data['nom'])) $errors['nom'] = "Nom obligatoire";
    elseif (strlen($data['nom']) < 2) $errors['nom'] = "Minimum 2 caractères";
    
    if (empty($data['prenom'])) $errors['prenom'] = "Prénom obligatoire";
    elseif (strlen($data['prenom']) < 2) $errors['prenom'] = "Minimum 2 caractères";
    
    if (empty($data['email'])) $errors['email'] = "Email obligatoire";
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide";
    elseif ($data['email'] != $user['email'] && $ctrl->emailExists($data['email'], $userId)) {
        $errors['email'] = "Email déjà utilisé";
    }
    
    if (empty($errors)) {
        $result = $ctrl->updateMyProfile($userId, $data);
        if ($result['success']) {
            $success = $result['message'];
            $user = $ctrl->getUserById($userId);
        } else {
            $error = $result['error'];
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .edit-card {
            max-width: 700px;
            margin: 50px auto;
            background: rgba(255,255,255,0.95);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .edit-card h2 {
            color: olivedrab;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
        }
        .form-group input:focus {
            border-color: olivedrab;
            outline: none;
        }
        .btn-save {
            background: olivedrab;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .btn-save:hover {
            background: #5a7a26;
        }
        .btn-cancel {
            background: #666;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<nav class="bord">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo-img">
    <div class="slogon">
        <h1>🌿 Green Assurance</h1>
        <small>Modifier mon profil</small>
    </div>
    <a href="profil.php?id=<?= $userId ?>"><i class="fas fa-arrow-left"></i> Retour au profil</a>
    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</nav>

<div class="page-wrapper">
    <div class="edit-card">
        <h2><i class="fas fa-user-edit"></i> Modifier mes informations</h2>
        
        <?php if ($success): ?>
            <div class="alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Nom *</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Prénom *</label>
                <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                <a href="profil.php?id=<?= $userId ?>" class="btn-cancel"><i class="fas fa-times"></i> Annuler</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>