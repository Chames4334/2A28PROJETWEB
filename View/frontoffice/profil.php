<?php
include_once '../../controller/ControlUser.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$ctrl = new ControlUser();
$id = $_GET['id'] ?? $_SESSION['user_id'];
$user = $ctrl->getUserById($id);

if (!$user) {
    echo "Utilisateur non trouvé";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - Green Assurance</title>
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
    <div class="slogon">
        <h1>🌿 Green Assurance</h1>
        <small>FrontOffice</small>
    </div>
    <a href="../backoffice/liste.php"><i class="fas fa-home"></i> Accueil</a>
    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</nav>

<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-banner"></div>
        <div class="profile-body">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
            </div>
            <h2 class="profile-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
            <span class="badge badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span>
            
            <div class="profile-info">
                <?php if(!empty($user['phone'])): ?>
                <div class="info-row"><i class="fas fa-phone"></i> <span><?= htmlspecialchars($user['phone']) ?></span></div>
                <?php endif; ?>
                <?php if(!empty($user['address'])): ?>
                <div class="info-row"><i class="fas fa-map-marker-alt"></i> <span><?= htmlspecialchars($user['address']) ?></span></div>
                <?php endif; ?>
                <div class="info-row"><i class="fas fa-calendar"></i> <span>Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?></span></div>
            </div>
            
            <div class="profile-actions">
                <a href="../backoffice/update.php?id=<?= $user['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier mon profil
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="footer"><p>&copy; 2024 Green Assurance - Tous droits réservés</p></footer>
<script src="../assets/validation.js"></script>
</body>
</html>