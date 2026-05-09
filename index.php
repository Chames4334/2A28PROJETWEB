<?php
// index.php - Point d'entrée unique
require_once __DIR__ . '/config.php';

// Rediriger vers la page d'accueil frontoffice ou login
if (isset($_SESSION['user_id'])) {
    header('Location: view/frontoffice/accueil.php');
} else {
    header('Location: view/auth/login.php');
}
exit;
?>