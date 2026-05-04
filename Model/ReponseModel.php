<?php
require_once __DIR__ . '/../Config/Database.php';

class ReponseModel {
    private $db;
    public function __construct() { $this->db = Database::getConnection(); }

    public function getByDemandeId($demande_id) {
        $stmt = $this->db->prepare("SELECT r.*, a.nom as atelier_nom FROM reponse_constat r LEFT JOIN ateliers a ON r.id_atelier = a.id WHERE r.demande_id = ?");
        $stmt->execute([$demande_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createOrUpdate($demande_id, $type_reponse, $montant, $id_atelier, $message_admin) {
        $existing = $this->getByDemandeId($demande_id);
        if ($existing) {
            $sql = "UPDATE reponse_constat SET type_reponse=?, montant=?, id_atelier=?, message_admin=? WHERE demande_id=?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$type_reponse, $montant, $id_atelier, $message_admin, $demande_id]);
        } else {
            $sql = "INSERT INTO reponse_constat (demande_id, type_reponse, montant, id_atelier, message_admin) VALUES (?,?,?,?,?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$demande_id, $type_reponse, $montant, $id_atelier, $message_admin]);
        }
    }
}
?>