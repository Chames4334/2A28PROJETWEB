<?php
// index.php - Point d'entrée unique (Routeur)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/UserController.php';

// Initialisation du contrôleur
$controller = new UserController($pdo);

// Récupérer l'action
$action = $_GET['action'] ?? 'list';
$page = $_GET['page'] ?? 'backoffice';

// =========================================================
// ROUTES BACKOFFICE
// =========================================================
if ($page === 'backoffice') {
    switch ($action) {
        case 'list':
            $controller->list();
            break;
        case 'create':
            $controller->createForm();
            break;
        case 'store':
            $controller->store();
            break;
        case 'edit':
            $controller->editForm();
            break;
        case 'update':
            $controller->update();
            break;
        case 'confirm-delete':
            $controller->confirmDelete();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            $controller->list();
    }
}
// =========================================================
// ROUTES FRONTOFFICE
// =========================================================
elseif ($page === 'frontoffice') {
    switch ($action) {
        case 'profile':
            $controller->profile();
            break;
        default:
            $controller->profile();
    }
}
// Par défaut -> backoffice
else {
    $controller->list();
}