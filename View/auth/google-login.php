<?php
// google-login.php - Bouton Se connecter avec Google
require_once 'google-config.php';

$client = getGoogleClient();
$auth_url = $client->createAuthUrl();
?>

<a href="<?= $auth_url ?>" style="display: flex; align-items: center; justify-content: center; gap: 10px; background: white; color: #757575; border: 1px solid #ddd; padding: 8px 0; border-radius: 5px; text-decoration: none; width: 100%; margin-top: 15px; font-weight: 500; font-size: 1rem;">
    <i class="fab fa-google" style="color: #DB4437;"></i>
    Se connecter avec Google
</a>