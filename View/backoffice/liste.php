<?php
include_once '../../controller/ControlUser.php';
$ctrl = new ControlUser();
$users = $ctrl->listeUser();
$stats = [
    'total' => $ctrl->countUsers(),
    'active' => $ctrl->countByStatus('active'),
    'blocked' => $ctrl->countByStatus('blocked'),
    'pending' => $ctrl->countByStatus('pending')
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs - Green Assurance</title>
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
        <small>BackOffice</small>
    </div>
    <a href="liste.php"><i class="fas fa-users"></i> Utilisateurs</a>
    <a href="ajout.php"><i class="fas fa-plus"></i> Nouveau</a>
    <a href="../frontoffice/profil.php?id=<?= $_SESSION['user_id'] ?? 1 ?>"><i class="fas fa-user"></i> Mon Profil</a>
    <a href="../auth/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</nav>

<div class="page-wrapper">

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-card stat-total">
        <i class="fas fa-users"></i>
        <div><span class="stat-num"><?= $stats['total'] ?></span><span class="stat-label">Total</span></div>
    </div>
    <div class="stat-card stat-active">
        <i class="fas fa-check-circle"></i>
        <div><span class="stat-num"><?= $stats['active'] ?></span><span class="stat-label">Actifs</span></div>
    </div>
    <div class="stat-card stat-blocked">
        <i class="fas fa-ban"></i>
        <div><span class="stat-num"><?= $stats['blocked'] ?></span><span class="stat-label">Bloqués</span></div>
    </div>
    <div class="stat-card stat-pending">
        <i class="fas fa-clock"></i>
        <div><span class="stat-num"><?= $stats['pending'] ?></span><span class="stat-label">En attente</span></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-list"></i> Liste des utilisateurs</h2>
        <a href="ajout.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvel utilisateur</a>
    </div>
    
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Rechercher...">
    </div>
    
    <div class="table-wrapper">
        <table class="data-table" id="usersTable">
            <thead>
                <tr><th>ID</th><th>Utilisateur</th><th>Email</th><th>Téléphone</th><th>Rôles</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td>
                        <div class="user-name-cell">
                            <div class="avatar"><?= strtoupper(substr($user['prenom'],0,1).substr($user['nom'],0,1)) ?></div>
                            <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                        </div>
                    </div>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '—') ?></td>
                    <td>
                        <?php if(!empty($user['roles_noms'])): ?>
                            <?php foreach(explode(', ', $user['roles_noms']) as $role): ?>
                                <span class="badge badge-role"><?= ucfirst($role) ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </div>
                    <td><span class="badge badge-<?= $user['status'] ?>"><?= ucfirst($user['status']) ?></span></td>
                    <td class="action-btns">
                        <a href="../frontoffice/profil.php?id=<?= $user['id'] ?>" class="btn-icon btn-view"><i class="fas fa-eye"></i></a>
                        <a href="update.php?id=<?= $user['id'] ?>" class="btn-icon btn-edit"><i class="fas fa-edit"></i></a>
                        <a href="delete.php?id=<?= $user['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                    </div>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<footer class="footer"><p>&copy; 2024 Green Assurance - Tous droits réservés</p></footer>
<script src="../assets/validation.js"></script>
</body>
</html>