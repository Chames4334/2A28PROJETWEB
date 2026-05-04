<?php
class TypeReponse {
    private $conn;
    private $table_name = "type_reponse";
    
    private $id;
    private $nom;
    private $description;
    private $categorie;
    private $gouvernorat;
    private $atelier_id;
    private $montant;
    private $created_at;

    // ============= CONSTRUCTEUR =============
    public function __construct($db) {
        $this->conn = $db;
    }

    // ============= GETTERS =============
    public function getId() { 
        return $this->id; 
    }
    
    public function getNom() { 
        return $this->nom; 
    }
    
    public function getDescription() { 
        return $this->description; 
    }
    
    public function getCategorie() { 
        return $this->categorie; 
    }
    
    public function getGouvernorat() { 
        return $this->gouvernorat; 
    }
    
    public function getAtelierId() { 
        return $this->atelier_id; 
    }
    
    public function getMontant() { 
        return $this->montant; 
    }
    
    public function getCreatedAt() { 
        return $this->created_at; 
    }
    
    public function getTableName() { 
        return $this->table_name; 
    }
    
    public function getConn() { 
        return $this->conn; 
    }

    // ============= SETTERS =============
    public function setId($id) {
        $this->id = (int)$id;
        return $this;
    }

    public function setNom($nom) {
        $this->nom = htmlspecialchars(strip_tags($nom));
        return $this;
    }

    public function setDescription($description) {
        $this->description = htmlspecialchars(strip_tags($description));
        return $this;
    }

    public function setCategorie($categorie) {
        $this->categorie = htmlspecialchars(strip_tags($categorie));
        return $this;
    }

    public function setGouvernorat($gouvernorat) {
        $this->gouvernorat = htmlspecialchars(strip_tags($gouvernorat));
        return $this;
    }

    public function setAtelierId($atelier_id) {
        $this->atelier_id = (int)$atelier_id;
        return $this;
    }

    public function setMontant($montant) {
        $this->montant = (float)$montant;
        return $this;
    }
    public function setCreatedAt($created_at) {
       $this->created_at = $created_at;
       return $this;
}
}
?>