<?php
require_once __DIR__ . '/controllers/CongeController.php';
require_once __DIR__ . '/controllers/TraitementCongeController.php';

$action = $_GET['action'] ?? null;
$page = $_GET['page'] ?? null;

if ($action) {
    // Routes pour Conge
    if (in_array($action, ['index', 'create', 'edit', 'delete', 'adminIndex'])) {
        $controller = new CongeController();

        switch ($action) {
            case 'index':
                $controller->index();
                break;
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
                if ($id) {
                    $controller->edit($id);
                } else {
                    header('Location: index.php?action=adminIndex');
                }
                break;
            case 'delete':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
                if ($id) {
                    $controller->delete($id);
                } else {
                    header('Location: index.php?action=adminIndex');
                }
                break;
            case 'adminIndex':
                $controller->adminIndex();
                break;
        }
    }
    // Routes pour TraitementConge
    elseif (in_array($action, ['traitementIndex', 'traitementCreate', 'traitementEdit', 'traitementDelete', 'traitementAdminIndex'])) {
        $controller = new TraitementCongeController();

        switch ($action) {
            case 'traitementIndex':
                $controller->index();
                break;
            case 'traitementCreate':
                $controller->create();
                break;
            case 'traitementEdit':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
                if ($id) {
                    $controller->edit($id);
                } else {
                    header('Location: index.php?action=traitementAdminIndex');
                }
                break;
            case 'traitementDelete':
                $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
                if ($id) {
                    $controller->delete($id);
                } else {
                    header('Location: index.php?action=traitementAdminIndex');
                }
                break;
            case 'traitementAdminIndex':
                $controller->adminIndex();
                break;
        }
    } else {
        header('Location: index.php?page=home');
    }
    exit;
}

$allowedPages = ['home', 'frontoffice', 'backoffice'];
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

include __DIR__ . '/views/' . $page . '.php';
