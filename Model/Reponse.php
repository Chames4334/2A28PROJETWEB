<?php
class Reponse {
    private $conn;
    private $table_name = "reponse_constat";
    
    private $id;
    private $demande_id;
    private $type_reponse_id;
    private $montant;
    private $id_atelier;
    private $message_admin;
    private $statut_voiture;
    private $temps_restant;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // GETTERS (UN SEUL CHAQUE)
    public function getId() { return $this->id; }
    public function getDemandeId() { return $this->demande_id; }
    public function getTypeReponseId() { return $this->type_reponse_id; }
    public function getMontant() { return $this->montant; }
    public function getIdAtelier() { return $this->id_atelier; }
    public function getMessageAdmin() { return $this->message_admin; }
    public function getStatutVoiture() { return $this->statut_voiture; }
    public function getTempsRestant() { return $this->temps_restant; }
    public function getCreatedAt() { return $this->created_at; }
    public function getTableName() { return $this->table_name; }
    public function getConn() { return $this->conn; }

    // SETTERS
    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }
    public function setDemandeId($demande_id) {
        $this->demande_id = (int)$demande_id;
        return $this;
    }
    public function setTypeReponseId($type_reponse_id) {
        $this->type_reponse_id = (int)$type_reponse_id;
        return $this;
    }
    public function setMontant($montant) {
        $this->montant = !empty($montant) ? (float)$montant : null;
        return $this;
    }
    public function setIdAtelier($id_atelier) {
        $this->id_atelier = !empty($id_atelier) ? (int)$id_atelier : null;
        return $this;
    }
    public function setMessageAdmin($message) {
        $this->message_admin = htmlspecialchars(strip_tags($message));
        return $this;
    }
    public function setStatutVoiture($statut) {
        $this->statut_voiture = htmlspecialchars(strip_tags($statut));
        return $this;
    }
    public function setTempsRestant($temps) {
        $this->temps_restant = !empty($temps) ? (int)$temps : null;
        return $this;
    }
}
?>