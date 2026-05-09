<?php
// google-callback.php - Traite le retour de Google
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'google-config.php';
require_once dirname(__DIR__, 2) . '/controller/ControlUser.php';

$client = getGoogleClient();

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);
        
        $oauth2 = new Google\Service\Oauth2($client);
        $user_info = $oauth2->userinfo->get();
        
        if ($user_info && $user_info->email) {
            $google_user = [
                'email' => $user_info->email,
                'nom' => $user_info->familyName ?? '',
                'prenom' => $user_info->givenName ?? '',
                'google_id' => $user_info->id,
                'picture' => $user_info->picture ?? ''
            ];
            
            $ctrl = new ControlUser();
            $result = $ctrl->loginWithGoogle($google_user);
            
            if ($result['success']) {
                if ($_SESSION['user_role'] === 'admin') {
                    header('Location: ../backoffice/liste.php');
                } else {
                    header('Location: ../frontoffice/accueil.php');
                }
                exit;
            } else {
                $_SESSION['google_error'] = $result['error'];
                header('Location: login.php?error=google_auth_failed');
                exit;
            }
        }
    } catch (Exception $e) {
        $_SESSION['google_error'] = $e->getMessage();
        header('Location: login.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

header('Location: login.php?error=No code received');
exit;
?>