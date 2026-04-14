<?php
require_once __DIR__ . '/controllers/CongeController.php';

$action = $_GET['action'] ?? null;
$page = $_GET['page'] ?? null;

if ($action) {
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
        default:
            header('Location: index.php?page=home');
            break;
    }
    exit;
}

$allowedPages = ['home', 'frontoffice', 'backoffice'];
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

include __DIR__ . '/views/' . $page . '.php';
