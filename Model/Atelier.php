<?php
class Atelier {
    private $conn;
    private $table_name = "ateliers";
    
    private $id;
    private $nom;
    private $adresse;
    private $telephone;
    private $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ============= GETTERS =============
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getAdresse() { return $this->adresse; }
    public function getTelephone() { return $this->telephone; }
    public function getCreatedAt() { return $this->created_at; }
    public function getTableName() { return $this->table_name; }
    public function getConn() { return $this->conn; }

    // ============= SETTERS =============
    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }

    public function setNom($nom) {
        $this->nom = htmlspecialchars(strip_tags($nom));
        return $this;
    }

    public function setAdresse($adresse) {
        $this->adresse = htmlspecialchars(strip_tags($adresse));
        return $this;
    }

    public function setTelephone($telephone) {
        $this->telephone = htmlspecialchars(strip_tags($telephone));
        return $this;
    }
}
?>