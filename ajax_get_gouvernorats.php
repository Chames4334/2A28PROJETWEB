<?php
header('Content-Type: application/json');
require_once 'Model/Config/Database.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT DISTINCT gouvernorat FROM ateliers WHERE gouvernorat IS NOT NULL AND gouvernorat <> '' ORDER BY gouvernorat";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($list);
} catch (Exception $e) {
    echo json_encode([]);
}
?>