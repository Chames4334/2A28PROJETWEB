<?php
require_once __DIR__ . '/../Config/Database.php';

class ConstatModel {
    private $db;
    public function __construct() { $this->db = Database::getConnection(); }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM demande_constat ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM demande_constat WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO demande_constat (nom, prenom, email, telephone, lieu_accident, date_accident, description, statut) VALUES (?,?,?,?,?,?,?,'en_attente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['telephone'], $data['lieu_accident'], $data['date_accident'], $data['description']]);
    }

    public function updateStatut($id, $statut) {
        $stmt = $this->db->prepare("UPDATE demande_constat SET statut = ? WHERE id = ?");
        return $stmt->execute([$statut, $id]);
    }
}
?>