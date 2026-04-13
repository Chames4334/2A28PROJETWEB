<?php
include_once '../../controller/ControlUser.php';
$ctrl = new ControlUser();
$roles = $ctrl->getAllRoles();

$id = $_GET['id'] ?? 0;
$user = $ctrl->getUserById($id);
if (!$user) {
    header('Location: liste.php');
    exit;
}

$userRoleIds = !empty($user['roles_ids']) ? explode(',', $user['roles_ids']) : [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $status = $_POST['status'] ?? 'pending';
    $selectedRoles = $_POST['roles'] ?? [];
    
    // Validation
    if (empty($nom)) $errors['nom'] = "Nom obligatoire";
    elseif (strlen($nom) < 2) $errors['nom'] = "Minimum 2 caractères";
    
    if (empty($prenom)) $errors['prenom'] = "Prénom obligatoire";
    elseif (strlen($prenom) < 2) $errors['prenom'] = "Minimum 2 caractères";
    
    if (empty($email)) $errors['email'] = "Email obligatoire";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email invalide";
    elseif ($ctrl->emailExists($email, $id)) $errors['email'] = "Email déjà utilisé";
    
    if (!empty($password)) {
        if (strlen($password) < 8) $errors['password'] = "Minimum 8 caractères";
        elseif (!preg_match('/[A-Z]/', $password)) $errors['password'] = "Au moins une majuscule";
        elseif (!preg_match('/[0-9]/', $password)) $errors['password'] = "Au moins un chiffre";
    }
    
    if (!empty($phone) && !preg_match('/^[0-9+\-\s]{8,15}$/', $phone)) {
        $errors['phone'] = "Format téléphone invalide";
    }
    
    if (empty($errors)) {
        $userObj = new User($nom, $prenom, $email, $password, $phone, $address, $status);
        if ($ctrl->updateUser($userObj, $id, $selectedRoles)) {
            $_SESSION['success'] = "Utilisateur modifié avec succès";
            header('Location: liste.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier utilisateur - Green Assurance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="animated-bg"></div>
<div class="floating-shape shape1"></div>
<div class="floating-shape shape2"></div>

<nav class="bord">
    <img src="../assets/logo.png" alt="Green Assurance" class="logo-img">
    <div class="slogon"><h1>🌿 Green Assurance</h1><small>BackOffice</small></div>
    <a href="liste.php"><i class="fas fa-arrow-left"></i> Retour</a>
</nav>

<div class="page-wrapper">
<div class="form-card">
    <div class="form-card-header">
        <h2><i class="fas fa-user-edit"></i> Modifier l'utilisateur #<?= $id ?></h2>
    </div>
    
    <form method="POST" action="" id="userForm" novalidate>
        <div class="form-grid">
            <div class="form-group <?= isset($errors['nom']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-user"></i> Nom *</label>
                <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($_POST['nom'] ?? $user['nom']) ?>">
                <?php if(isset($errors['nom'])): ?><span class="error-msg"><?= $errors['nom'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-group <?= isset($errors['prenom']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-user"></i> Prénom *</label>
                <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? $user['prenom']) ?>">
                <?php if(isset($errors['prenom'])): ?><span class="error-msg"><?= $errors['prenom'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-envelope"></i> Email *</label>
                <input type="text" name="email" id="email" value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
                <?php if(isset($errors['email'])): ?><span class="error-msg"><?= $errors['email'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-group <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-phone"></i> Téléphone</label>
                <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone'] ?? '') ?>">
                <?php if(isset($errors['phone'])): ?><span class="error-msg"><?= $errors['phone'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                <input type="password" name="password" id="password">
                <small>Laisser vide pour ne pas changer</small>
                <?php if(isset($errors['password'])): ?><span class="error-msg"><?= $errors['password'] ?></span><?php endif; ?>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-toggle-on"></i> Statut</label>
                <select name="status">
                    <option value="active" <?= (($_POST['status'] ?? $user['status']) == 'active') ? 'selected' : '' ?>>Actif</option>
                    <option value="blocked" <?= (($_POST['status'] ?? $user['status']) == 'blocked') ? 'selected' : '' ?>>Bloqué</option>
                    <option value="pending" <?= (($_POST['status'] ?? $user['status']) == 'pending') ? 'selected' : '' ?>>En attente</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-map-marker-alt"></i> Adresse</label>
            <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? $user['address'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label><i class="fas fa-shield-alt"></i> Rôles</label>
            <div class="roles-grid">
                <?php foreach($roles as $role): ?>
                    <?php $checked = in_array($role['id'], $_POST['roles'] ?? $userRoleIds); ?>
                    <label class="role-checkbox">
                        <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" <?= $checked ? 'checked' : '' ?>>
                        <span><?= ucfirst($role['nom']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="liste.php" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
</div>

<script src="../assets/validation.js"></script>
</body>
</html>