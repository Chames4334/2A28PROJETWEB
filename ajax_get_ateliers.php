<?php
header('Content-Type: application/json');
require_once 'Model/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

$gouvernorat = $_GET['gouvernorat'] ?? '';

$query = "SELECT id, nom, adresse FROM ateliers WHERE gouvernorat = :gouvernorat";
$stmt = $db->prepare($query);
$stmt->bindParam(':gouvernorat', $gouvernorat);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>