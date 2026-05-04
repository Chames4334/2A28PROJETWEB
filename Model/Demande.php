<?php
class Demande {
    private $conn;
    private $table_name = "demande_constat";
    
    // Attributs privés
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $lieu_accident;
    private $date_accident;
    private $description;
    private $statut;
    private $created_at;

    // ============= CONSTRUCTEUR =============
    public function __construct($db) {
        $this->conn = $db;
    }

    // ============= GETTERS =============
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getLieuAccident() { return $this->lieu_accident; }
    public function getDateAccident() { return $this->date_accident; }
    public function getDescription() { return $this->description; }
    public function getStatut() { return $this->statut; }
    public function getCreatedAt() { return $this->created_at; }
    public function getTableName() { return $this->table_name; }
    public function getConn() { return $this->conn; }

    // ============= SETTERS =============
    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }

    public function setNom($nom) {
        $this->nom = htmlspecialchars(strip_tags(trim($nom)));
        return $this;
    }

    public function setPrenom($prenom) {
        $this->prenom = htmlspecialchars(strip_tags(trim($prenom)));
        return $this;
    }

    public function setEmail($email) {
        $this->email = htmlspecialchars(strip_tags($email));
        return $this;
    }

    public function setTelephone($telephone) {
        $this->telephone = htmlspecialchars(strip_tags($telephone));
        return $this;
    }

    public function setLieuAccident($lieu) {
        $this->lieu_accident = htmlspecialchars(strip_tags($lieu));
        return $this;
    }

    public function setDateAccident($date) {
        $this->date_accident = htmlspecialchars(strip_tags($date));
        return $this;
    }

    public function setDescription($description) {
        $this->description = htmlspecialchars(strip_tags($description));
        return $this;
    }

    public function setStatut($statut) {
        $allowed = ['soumis', 'en_cours', 'accepte', 'refuse', 'clos'];
        $this->statut = in_array($statut, $allowed) ? $statut : 'soumis';
        return $this;
    }
    public function setCreatedAt($created_at) {
    $this->created_at = $created_at;
    return $this;
}
}
?>