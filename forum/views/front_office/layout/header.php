<?php
declare(strict_types=1);

$forumRoot = dirname(__DIR__, 3);
if (!function_exists('flash_get')) {
    require_once $forumRoot . '/core/helpers.php';
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!class_exists('User', false)) {
    require_once $forumRoot . '/models/User.php';
}

/** @var string $pageTitle */
global $currentForumUser, $currentUserIsAdmin;
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? 'Forum') ?></title>
    <link rel="stylesheet" href="<?= h(forum_asset_url('css/forum.css')) ?>">
</head>
<body class="forum-body">
<header class="site-header">
    <div class="site-header__inner">
        <a class="site-logo" href="index.php">Forum communauté</a>
        <nav class="site-nav" aria-label="Principal">
            <a href="index.php">Sujets</a>
            <?php if ($currentForumUser !== null) : ?>
                <a href="create.php">Nouveau sujet</a>
                <a href="mine.php">Mes sujets</a>
                <?php if (!empty($currentUserIsAdmin ?? false)) : ?>
                    <a href="admin_dashboard.php">Admin</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
        <div class="site-auth">
            <?php if ($currentForumUser !== null) : ?>
                <span class="site-auth__user"><?= h(User::displayName($currentForumUser)) ?></span>
                <a class="btn btn--secondary btn--small" href="logout.php">Déconnexion</a>
            <?php else : ?>
                <a class="btn btn--secondary btn--small" href="login.php">Connexion</a>
                <a class="btn btn--small" href="register.php">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if ($flash !== null) : ?>
    <div class="flash flash--<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></div>
<?php endif; ?>

<main class="main-content<?= ($layoutMainClass ?? '') !== '' ? ' ' . h((string) $layoutMainClass) : '' ?>">
