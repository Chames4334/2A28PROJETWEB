<?php
require_once 'Model/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

$query = "INSERT INTO demande_constat (nom, prenom, email, telephone, lieu_accident, date_accident, description, statut) 
          VALUES (:nom, :prenom, :email, :telephone, :lieu_accident, :date_accident, :description, 'soumis')";
$stmt = $db->prepare($query);
$stmt->execute([
    ':nom' => $_POST['nom'],
    ':prenom' => $_POST['prenom'],
    ':email' => $_POST['email'],
    ':telephone' => $_POST['telephone'],
    ':lieu_accident' => $_POST['lieu_accident'],
    ':date_accident' => $_POST['date_accident'],
    ':description' => $_POST['description']
]);

$demande_id = $db->lastInsertId();

// Send Email Auto-Send
require_once 'Controller/NotificationController.php';
$notifController = new NotificationController($db);
$notifController->notifierConfirmationDemande(
    $demande_id, 
    $_POST['email'], 
    $_POST['nom'], 
    $_POST['prenom']
);

header("Location: index.php?action=historique&success=1");
exit();
?>