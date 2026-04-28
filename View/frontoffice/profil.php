=<?php
include_once '../../controller/ControlUser.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$upload_error = '';
$upload_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $result = $ctrl->uploadProfilePhoto($id, $_FILES['profile_photo']);
    if ($result['success']) {
        $upload_success = "Photo de profil mise à jour !";
        $user = $ctrl->getUserById($id);
    } else {
        $upload_error = $result['error'];
    }
}

$profilePhoto = BASE_URL . "view/assets/uploads/" . ($user['profile_photo'] ?? '');
if (empty($user['profile_photo'])) {
    $profilePhoto = BASE_URL . "view/assets/logo.png";
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
    <style>
        .profile-photo {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-photo-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid olivedrab;
            background: white;
        }
        .upload-form {
            margin-top: 10px;
        }
        .upload-form input[type="file"] {
            margin: 10px 0;
        }
        .btn-upload {
            background: olivedrab;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
        }
        .btn-danger {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
        }
        .btn-danger:hover {
            background: #ee5a5a;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
        }
        .profile-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            text-align: center;
        }
        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            justify-content: center;
        }
    </style>
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
    <a href="accueil.php"><i class="fas fa-home"></i> Accueil</a>
    <a href="../backoffice/liste.php"><i class="fas fa-users"></i> Administration</a>
    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</nav>

<div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-banner"></div>
        <div class="profile-body">
            
            <div class="profile-photo">
                <img src="<?= $profilePhoto ?>" alt="Photo de profil" class="profile-photo-img">
                
                <form method="POST" action="" enctype="multipart/form-data" class="upload-form">
                    <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/gif">
                    <br>
                    <button type="submit" class="btn-upload"><i class="fas fa-upload"></i> Changer ma photo</button>
                </form>
                
                <?php if ($upload_error): ?>
                    <div style="color: red; font-size: 0.8rem; margin-top: 5px;"><?= $upload_error ?></div>
                <?php endif; ?>
                <?php if ($upload_success): ?>
                    <div style="color: green; font-size: 0.8rem; margin-top: 5px;"><?= $upload_success ?></div>
                <?php endif; ?>
            </div>
            
            <div class="profile-avatar" style="display: none;">
                <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
            </div>
            
            <h2 class="profile-name"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
            
            <?php if($user['email_verified'] == 1): ?>
                <span class="badge badge-active"><i class="fas fa-check-circle"></i> Email vérifié</span>
            <?php else: ?>
                <span class="badge badge-pending"><i class="fas fa-clock"></i> Email non vérifié</span>
            <?php endif; ?>
            
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
                <a href="edit_profile.php" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier mes informations
                </a>
                <button onclick="showDeactivateModal()" class="btn-danger">
                    <i class="fas fa-ban"></i> Désactiver mon compte
                </button>
                <button onclick="showDeleteModal()" class="btn-danger" style="background:#cc0000;">
                    <i class="fas fa-trash"></i> Supprimer mon compte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Désactivation -->
<div id="deactivateModal" class="modal">
    <div class="modal-content">
        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: orange;"></i>
        <h3>Désactiver mon compte</h3>
        <p>Êtes-vous sûr de vouloir désactiver votre compte ?</p>
        <p style="color: red; font-size: 0.8rem;">Vous ne pourrez plus vous connecter jusqu'à réactivation.</p>
        <div class="modal-buttons">
            <button onclick="closeModals()" class="btn btn-secondary">Annuler</button>
            <a href="deactivate_account.php" class="btn-danger" style="text-decoration: none;">Confirmer</a>
        </div>
    </div>
</div>

<!-- Modal Suppression -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <i class="fas fa-skull-crossbones" style="font-size: 3rem; color: red;"></i>
        <h3>Supprimer définitivement mon compte</h3>
        <p>Cette action est <strong>IRRÉVERSIBLE</strong> !</p>
        <p>Toutes vos données seront perdues.</p>
        <div class="modal-buttons">
            <button onclick="closeModals()" class="btn btn-secondary">Annuler</button>
            <a href="delete_account.php" class="btn-danger" style="background: #cc0000; text-decoration: none;">Confirmer</a>
        </div>
    </div>
</div>

<script>
    function showDeactivateModal() {
        document.getElementById('deactivateModal').style.display = 'flex';
    }
    function showDeleteModal() {
        document.getElementById('deleteModal').style.display = 'flex';
    }
    function closeModals() {
        document.getElementById('deactivateModal').style.display = 'none';
        document.getElementById('deleteModal').style.display = 'none';
    }
</script>

<footer class="footer"><p>&copy; 2024 Green Assurance - Tous droits réservés</p></footer>
<script src="../assets/validation.js"></script>
</body>
</html>