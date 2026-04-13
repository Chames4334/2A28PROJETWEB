<?php
/**
 * Initialisation : session, configuration, PDO, autoload minimal.
 */

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$forumRoot = dirname(__DIR__);

if (!defined('FORUM_ROOT')) {
    define('FORUM_ROOT', $forumRoot);
}
/* Toujours définir FORUM_VIEWS si absent (FORUM_ROOT peut exister sans FORUM_VIEWS) */
if (!defined('FORUM_VIEWS')) {
    define('FORUM_VIEWS', $forumRoot . '/views');
}

require_once $forumRoot . '/config/app.php';
require_once $forumRoot . '/core/helpers.php';

$dbConfig = require $forumRoot . '/config/database.php';

try {
    $pdo = new PDO(
        $dbConfig['dsn'],
        $dbConfig['user'],
        $dbConfig['password'],
        $dbConfig['options']
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Erreur de connexion à la base de données.';
    exit;
}

/** @var PDO $pdo Connexion PDO partagée par les modèles (inclus après bootstrap) */

// Fallback développement — voir config/app.php
if (
    defined('FORUM_DEV_AUTO_USER_ID')
    && FORUM_DEV_AUTO_USER_ID !== false
    && (int) FORUM_DEV_AUTO_USER_ID > 0
    && empty($_SESSION['user_id'])
) {
    $_SESSION['user_id'] = (int) FORUM_DEV_AUTO_USER_ID;
}

spl_autoload_register(static function (string $class) use ($forumRoot): void {
    $paths = [
        $forumRoot . '/models/' . $class . '.php',
        $forumRoot . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

require_once $forumRoot . '/core/app.php';

/** Utilisateur connecté (ligne users) pour l’en-tête, ou null */
$currentForumUser = null;
if (($__uid = current_user_id()) !== null) {
    $currentForumUser = (new User($pdo))->findById($__uid);
}
