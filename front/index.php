<?php
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require_once ROOT_PATH . 'Model/Config/Database.php';
require_once ROOT_PATH . 'Model/Demande.php';
require_once ROOT_PATH . 'Model/Reponse.php';
require_once ROOT_PATH . 'Model/TypeReponse.php';
require_once ROOT_PATH . 'Model/Atelier.php';

$database = new Database();
$db = $database->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : 'accueil';

// =============================================================
// ROUTES FRONT OFFICE
// =============================================================

// CREATE - Ajouter une demande
if ($action == 'save_declaration' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "INSERT INTO demande_constat 
              SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                  lieu_accident=:lieu_accident, date_accident=:date_accident,
                  description=:description, statut='soumis'";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":nom", $_POST['nom']);
    $stmt->bindParam(":prenom", $_POST['prenom']);
    $stmt->bindParam(":email", $_POST['email']);
    $stmt->bindParam(":telephone", $_POST['telephone']);
    $stmt->bindParam(":lieu_accident", $_POST['lieu_accident']);
    $stmt->bindParam(":date_accident", $_POST['date_accident']);
    $stmt->bindParam(":description", $_POST['description']);
    
   if ($stmt->execute()) {
    $demande_id = $db->lastInsertId();
    
    // N'insérer dans reponse_constat que si un type de réponse a été choisi
    if (!empty($_POST['type_reponse_id'])) {
        $query2 = "INSERT INTO reponse_constat 
                   SET demande_id=:demande_id, type_reponse_id=:type_reponse_id,
                       montant=:montant, id_atelier=:id_atelier, message_admin=:message_admin";
        $stmt2 = $db->prepare($query2);
        
        $montant    = !empty($_POST['montant'])    ? $_POST['montant']    : null;
        $id_atelier = !empty($_POST['id_atelier']) ? $_POST['id_atelier'] : null;
        $message    = isset($_POST['message_reponse']) ? $_POST['message_reponse'] : '';
        
        $stmt2->bindValue(":demande_id",      $demande_id,                    PDO::PARAM_INT);
        $stmt2->bindValue(":type_reponse_id", (int)$_POST['type_reponse_id'], PDO::PARAM_INT);
        $stmt2->bindValue(":montant",         $montant,    $montant    !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt2->bindValue(":id_atelier",      $id_atelier, $id_atelier !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt2->bindValue(":message_admin",   $message,                       PDO::PARAM_STR);
        $stmt2->execute();
    }
    
    header("Location: /gs_assurance/index.php?action=demandes&success=1");
    exit();
}
}

// UPDATE - Modifier une demande
if ($action == 'update_declaration' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "UPDATE demande_constat 
              SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                  lieu_accident=:lieu_accident, date_accident=:date_accident,
                  description=:description, statut=:statut
              WHERE id = :id";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":id", $_POST['id']);
    $stmt->bindParam(":nom", $_POST['nom']);
    $stmt->bindParam(":prenom", $_POST['prenom']);
    $stmt->bindParam(":email", $_POST['email']);
    $stmt->bindParam(":telephone", $_POST['telephone']);
    $stmt->bindParam(":lieu_accident", $_POST['lieu_accident']);
    $stmt->bindParam(":date_accident", $_POST['date_accident']);
    $stmt->bindParam(":description", $_POST['description']);
    $stmt->bindParam(":statut", $_POST['statut']);
    
    if ($stmt->execute()) {
        // Mettre à jour ou créer la réponse
        $checkQuery = "SELECT id FROM reponse_constat WHERE demande_id = :demande_id";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(":demande_id", $_POST['id']);
        $checkStmt->execute();
        $exists = $checkStmt->fetch();
        
        $montant = !empty($_POST['montant']) ? $_POST['montant'] : null;
        $id_atelier = !empty($_POST['id_atelier']) ? $_POST['id_atelier'] : null;
        $message = isset($_POST['message_reponse']) ? $_POST['message_reponse'] : '';
        
        if ($exists) {
            $query2 = "UPDATE reponse_constat 
                       SET type_reponse_id=:type_reponse_id, montant=:montant, 
                           id_atelier=:id_atelier, message_admin=:message_admin
                       WHERE demande_id = :demande_id";
            $stmt2 = $db->prepare($query2);
            $stmt2->bindParam(":demande_id", $_POST['id']);
        } else {
            $query2 = "INSERT INTO reponse_constat 
                       SET demande_id=:demande_id, type_reponse_id=:type_reponse_id,
                           montant=:montant, id_atelier=:id_atelier, message_admin=:message_admin";
            $stmt2 = $db->prepare($query2);
            $stmt2->bindParam(":demande_id", $_POST['id']);
        }
        
        if (!empty($_POST['type_reponse_id'])) {
            $stmt2->bindValue(":type_reponse_id", (int)$_POST['type_reponse_id'], PDO::PARAM_INT);
        } else {
            $stmt2->bindValue(":type_reponse_id", null, PDO::PARAM_NULL);
        }
        if ($montant !== null) {
            $stmt2->bindValue(":montant", $montant);
        } else {
            $stmt2->bindValue(":montant", null, PDO::PARAM_NULL);
        }
        if (!empty($id_atelier)) {
            $stmt2->bindValue(":id_atelier", (int)$id_atelier, PDO::PARAM_INT);
        } else {
            $stmt2->bindValue(":id_atelier", null, PDO::PARAM_NULL);
        }
        $stmt2->bindValue(":message_admin", $message, PDO::PARAM_STR);
        $stmt2->execute();
        
        header("Location: /gs_assurance/index.php?action=demandes&update=1");
        exit();
    }
}
// DELETE - Supprimer une demande
if ($action == 'delete_declaration') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $query2 = "DELETE FROM reponse_constat WHERE demande_id = :id";
        $stmt2 = $db->prepare($query2);
        $stmt2->bindParam(":id", $id);
        $stmt2->execute();
        
        $query = "DELETE FROM demande_constat WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        header("Location: /gs_assurance/index.php?action=demandes&delete=1");
        exit();
    }
}

// =============================================================
// AFFICHAGE DES PAGES
// =============================================================

if ($action == 'accueil') {
    include ROOT_PATH . 'front/pages/accueil.php';
} 
elseif ($action == 'declaration') {
    // Requête SQL directe pour les types de réponse (pas de méthode dans le Model)
    $queryTypes = "SELECT * FROM type_reponse ORDER BY id";
    $stmtTypes = $db->prepare($queryTypes);
    $stmtTypes->execute();
    $types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);
    
    // Requête SQL directe pour les ateliers
    $queryAteliers = "SELECT * FROM ateliers ORDER BY nom";
    $stmtAteliers = $db->prepare($queryAteliers);
    $stmtAteliers->execute();
    $ateliers = $stmtAteliers->fetchAll(PDO::FETCH_ASSOC);
    
    include ROOT_PATH . 'front/pages/declaration.php';
} 
elseif ($action == 'historique') {
    $query = "SELECT * FROM demande_constat ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    include ROOT_PATH . 'front/pages/historique.php';
} 
elseif ($action == 'show') {
    include ROOT_PATH . 'front/pages/show.php';
} 
elseif ($action == 'edit') {
    include ROOT_PATH . 'front/pages/edit.php';
} 
else {
    include ROOT_PATH . 'front/pages/accueil.php';
}
?>