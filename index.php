<?php
session_start();

require_once __DIR__ . '/controllers/CongeController.php';

$action = $_GET['action'] ?? null;
$page = $_GET['page'] ?? null;

if ($action) {

    $controller = new CongeController();

    switch ($action) {

        case 'index':
            $controller->index();
            break;

        case 'congePdf':
            $controller->exportPdf();
            break;

        case 'ai_analyze':
            require_once __DIR__ . '/controllers/AIController.php';
            (new AIController())->analyze();
            break;

        case 'ai_suggestions':
            require_once __DIR__ . '/controllers/AIController.php';
            (new AIController())->getSmartSuggestions();
            break;

        case 'ai_calendar_analysis':
            require_once __DIR__ . '/controllers/AIController.php';
            (new AIController())->analyzeCalendar();
            break;

        case 'ai_report_view':
            include 'views/backoffice/ai_report_view.php';
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

        case 'editTraitement':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

            if ($id) {
                $controller->editTraitement($id);
            } else {
                header('Location: index.php?action=adminIndex');
            }
            break;

        case 'delete':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

            if ($id) {
                $controller->deleteAction($id);
            } else {
                header('Location: index.php?action=adminIndex');
            }
            break;

        case 'adminIndex':
            $controller->adminIndex();
            break;

        case 'calendarAdmin':
            $controller->calendarAdmin();
            break;
    }

    exit;
}

$allowedPages = ['home', 'frontoffice', 'backoffice'];

if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

include __DIR__ . '/views/' . $page . '.php';
?>