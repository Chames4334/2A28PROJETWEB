<?php
// Créer le dossier logs s'il n'existe pas
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

session_start();
define('ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

require_once ROOT_PATH . 'Model/Config/Database.php';
require_once ROOT_PATH . 'Model/Demande.php';
require_once ROOT_PATH . 'Model/Atelier.php';
require_once ROOT_PATH . 'Model/Reponse.php';
require_once ROOT_PATH . 'Model/TypeReponse.php';
require_once ROOT_PATH . 'Controller/DemandeController.php';
require_once ROOT_PATH . 'Controller/ReponseController.php';
require_once ROOT_PATH . 'Controller/TypeReponseController.php';
require_once ROOT_PATH . 'Controller/NotificationController.php';
require_once ROOT_PATH . 'Controller/ChatbotController.php';
require_once ROOT_PATH . 'Controller/PdfController.php';

$database = new Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : 'demandes';

switch ($action) {
    // DEMANDES
   case 'demandes':
    $controller = new DemandeController($db);
    $controller->index();
    break;
    case 'create_demande':
        $controller = new DemandeController($db);
        $controller->create();
        break;
    case 'store_demande':
        $controller = new DemandeController($db);
        $controller->store();
        break;
    case 'edit_demande':
        $controller = new DemandeController($db);
        $controller->edit($_GET['id']);
        break;
    case 'update_demande':
        $controller = new DemandeController($db);
        $controller->update($_GET['id']);
        break;
    case 'show_demande':
        $controller = new DemandeController($db);
        $controller->show($_GET['id']);
        break;
    case 'delete_demande':
        $controller = new DemandeController($db);
        $controller->delete($_GET['id']);
        break;
    case 'store_demande_quick':
        $controller = new DemandeController($db);
        $controller->storeFromType();
        break;

    // TYPES DE REPONSE
    case 'type_reponses':
        $controller = new TypeReponseController($db);
        $controller->index();
        break;
    case 'create_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->create();
        break;
    case 'store_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->store();
        break;
    case 'edit_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->edit($_GET['id']);
        break;
    case 'update_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->update($_GET['id']);
        break;
    case 'show_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->show($_GET['id']);
        break;
    case 'delete_type_reponse':
        $controller = new TypeReponseController($db);
        $controller->delete($_GET['id']);
        break;

    // REPONSES
    case 'reponses':
        $controller = new ReponseController($db);
        $controller->index();
        break;
    case 'create_reponse':
        $controller = new ReponseController($db);
        $controller->create();
        break;
    case 'store_reponse':
        $controller = new ReponseController($db);
        $controller->store();
        break;
    case 'edit_reponse':
        $controller = new ReponseController($db);
        $controller->edit($_GET['id']);
        break;
    case 'update_reponse':
        $controller = new ReponseController($db);
        $controller->update($_GET['id']);
        break;
    case 'delete_reponse':
        $controller = new ReponseController($db);
        $controller->delete($_GET['id']);
        break;

    // STATISTIQUES
    case 'stats_type_reponse':
        include ROOT_PATH . 'View/statistiques/type_reponse.php';
        break;

    // CHATBOT
    case 'chatbot':
        $controller = new ChatbotController($db);
        $controller->index();
        break;
    case 'chatbot_send':
        header('Content-Type: application/json');
        $controller = new ChatbotController($db);
        $controller->chat();
        exit();
        break;
    case 'chatbot_clear':
        header('Content-Type: application/json');
        $controller = new ChatbotController($db);
        $controller->clear();
        exit();
        break;

    // PDF GENERATION
    case 'generate_pdf':
        $controller = new PdfController($db);
        $controller->generate($_GET['id'] ?? 0);
        exit();
        break;

    // NOTIFICATIONS
    case 'send_notification':
        $notif = new NotificationController($db);
        $type = $_GET['type'] ?? '';
        $id = $_GET['id'] ?? 0;
        
        if($type == 'prise_en_charge') {
            $notif->notifierPriseEnCharge($id);
        } elseif($type == 'voiture_prete') {
            $notif->notifierVoiturePrete($id);
        } elseif($type == 'rappel') {
            $notif->notifierRappel($id);
        }
        
        header("Location: index.php?action=edit_reponse&id=" . $id);
        exit();
        break;

    // FRONT OFFICE
    case 'accueil':
        include ROOT_PATH . 'front/pages/accueil.php';
        break;
    case 'declaration':
        $queryTypes = "SELECT * FROM type_reponse ORDER BY id";
        $stmtTypes = $db->prepare($queryTypes);
        $stmtTypes->execute();
        $types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
        
        $queryAteliers = "SELECT * FROM ateliers ORDER BY nom";
        $stmtAteliers = $db->prepare($queryAteliers);
        $stmtAteliers->execute();
        $ateliers = $stmtAteliers->fetchAll(PDO::FETCH_ASSOC);
        
        include ROOT_PATH . 'front/pages/declaration.php';
        break;
    case 'historique':
        $query = "SELECT * FROM demande_constat ORDER BY created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include ROOT_PATH . 'front/pages/historique.php';
        break;
    case 'front_all':
        include ROOT_PATH . 'front/pages/all.php';
        break;
    case 'show':
        include ROOT_PATH . 'front/pages/show.php';
        break;
    case 'edit':
        include ROOT_PATH . 'front/pages/edit.php';
        break;
    case 'save_declaration':
        include ROOT_PATH . 'save_declaration.php';
        break;

    default:
        $controller = new DemandeController($db);
        $controller->index();
        break;
    case 'send_contact':
        require_once ROOT_PATH . 'Controller/NotificationController.php';
        $notif = new NotificationController($db);
        $type = $_GET['type'] ?? '';
        $tel = $_GET['tel'] ?? '';
        $email = $_GET['email'] ?? '';
        $notif->envoyerContactParTel($tel, $email, $type);
        header("Location: index.php?action=demandes");
        exit();
        break;
}
?>