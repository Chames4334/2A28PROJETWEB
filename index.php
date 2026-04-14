<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/Controller/ConstatController.php';

$controller = new ConstatController();
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home': $controller->home(); break;
    case 'demande': $controller->demande(); break;
    case 'soumettre': $controller->soumettre(); break;
    case 'historique': $controller->historique(); break;
    case 'liste': $controller->liste(); break;
    case 'voir_reponse': $controller->voirReponse(); break;
    case 'reponse_form': $controller->reponseForm(); break;
    case 'reponse_submit': $controller->reponseSubmit(); break;
    default: $controller->home(); break;
}
?>