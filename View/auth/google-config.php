<?php
// google-config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure l'autoload de Composer
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// === REMPLACE PAR TES IDENTIFIANTS ===
define('GOOGLE_CLIENT_ID', 'AA');
define('GOOGLE_CLIENT_SECRET', 'AA');
define('GOOGLE_REDIRECT_URI', 'http://localhost/green_assurance/view/auth/google-callback.php');

function getGoogleClient() {
    $client = new Google\Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->addScope('email');
    $client->addScope('profile');
    $client->setAccessType('offline');
    $client->setPrompt('select_account');
    
    return $client;
}
?>