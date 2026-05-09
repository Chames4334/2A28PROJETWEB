<?php
// Simple test endpoint to send an email and display debug output.
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Controller/NotificationController.php';

// Allow ?to= and ?subject=
$to = $_GET['to'] ?? null;
$subject = $_GET['subject'] ?? 'Test email AS Assurance';
$body = $_GET['body'] ?? "Test email envoyé depuis test_email_debug.php";

if (empty($to)) {
    echo "Usage: test_email_debug.php?to=you@example.com&subject=...&body=...";
    exit;
}

$databasePath = __DIR__ . '/Model/Config/Database.php';
if (file_exists($databasePath)) {
    require_once $databasePath;
}

$db = null;
try {
    if (class_exists('Database')) {
        $dbObj = new Database();
        $db = $dbObj->getConnection();
    }
} catch (Exception $e) {
    // ignore
}

$notif = new NotificationController($db);
$res = $notif->envoyerEmail($to, $subject, $body);

echo '<h2>Résultat</h2>';
echo '<pre>' . htmlspecialchars(print_r($res, true)) . '</pre>';

echo '<h3>Vérifications à effectuer</h3>';
echo '<ul>';
echo '<li>Assurez-vous que `Config/SmtpConfig.php` contient le mot de passe d\'application Gmail (2FA + App Password).</li>';
echo '<li>Activez l\'extension OpenSSL dans `php.ini` (décommentez extension=openssl).</li>';
echo '<li>Redémarrez Apache via le panneau XAMPP après tout changement.</li>';
echo '</ul>';
