<?php
require_once __DIR__ . '/../Config/Database.php';

class AtelierModel {
    private $db;
    public function __construct() { $this->db = Database::getConnection(); }
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM ateliers ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>