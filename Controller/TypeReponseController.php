<?php
class TypeReponseController {
    private $conn;
    private $typeReponse;

    public function __construct($db) {
        $this->conn = $db;
        $this->typeReponse = new TypeReponse($this->conn);
    }

    // Afficher la liste des types
    public function index() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id_asc';
        
        $query = "SELECT * FROM " . $this->typeReponse->getTableName();
        
        if (!empty($search)) {
            $query .= " WHERE nom LIKE :search";
        }
        
        switch ($sort) {
            case 'nom_asc':
                $query .= " ORDER BY nom ASC";
                break;
            case 'nom_desc':
                $query .= " ORDER BY nom DESC";
                break;
            case 'date_asc':
                $query .= " ORDER BY created_at ASC";
                break;
            case 'date_desc':
                $query .= " ORDER BY created_at DESC";
                break;
            default:
                $query .= " ORDER BY id ASC";
                break;
        }
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(":search", $searchTerm);
        }
        
        $stmt->execute();
        $typeReponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // If requested as partial (AJAX modal), include lightweight fragment
        if (isset($_GET['partial']) && $_GET['partial']) {
            include ROOT_PATH . 'View/type_reponses/partial.php';
            return;
        }
        include ROOT_PATH . 'View/type_reponses/index.php';
    }

    // Afficher le formulaire de création
    public function create() {
        include ROOT_PATH . 'View/type_reponses/create.php';
    }

    // Enregistrer un nouveau type
    public function store() {
        $nom = $_POST['nom'] ?? '';
        $description = $_POST['description'] ?? '';
        $categorie = $_POST['categorie'] ?? 'remboursement';
        
        $query = "INSERT INTO " . $this->typeReponse->getTableName() . " 
                  SET nom = :nom, description = :description, categorie = :categorie";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":categorie", $categorie);
        $stmt->execute();
        
        header("Location: index.php?action=type_reponses&success=1");
        exit();
    }

    // Afficher les détails d'un type
   public function show($id) {
    $query = "SELECT * FROM " . $this->typeReponse->getTableName() . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $this->typeReponse->setId($row['id'])
                         ->setNom($row['nom'])
                         ->setDescription($row['description'])
                         ->setCategorie($row['categorie'] ?? '')
                         ->setGouvernorat($row['gouvernorat'] ?? '')
                         ->setAtelierId($row['atelier_id'] ?? null)
                         ->setMontant($row['montant'] ?? 0)
                         ->setCreatedAt($row['created_at']);  // <-- AJOUTEZ CETTE LIGNE
        include ROOT_PATH . 'View/type_reponses/show.php';
    } else {
        header("Location: index.php?action=type_reponses");
    }
}
    // Afficher le formulaire de modification
    public function edit($id) {
        $query = "SELECT * FROM " . $this->typeReponse->getTableName() . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->typeReponse->setId($row['id'])
                             ->setNom($row['nom'])
                             ->setDescription($row['description'])
                             ->setCategorie($row['categorie'] ?? '');
            include ROOT_PATH . 'View/type_reponses/edit.php';
        } else {
            header("Location: index.php?action=type_reponses");
        }
    }

    // Mettre à jour un type
    public function update($id) {
    try {
        $categorie = $_POST['categorie'] ?? 'remboursement';
        $gouvernorat = $_POST['gouvernorat'] ?? null;
        $id_atelier = $_POST['id_atelier'] ?? null;
        $montant = $_POST['montant'] ?? null;
        
        // Générer le nom automatiquement
        if ($categorie == 'atelier') {
            $nom = 'Atelier - ' . ($gouvernorat ?? 'Non défini');
            $description = 'Prise en charge par un atelier partenaire';
        } else {
            $nom = 'Remboursement - ' . ($montant ?? 'Montant variable') . ' TND';
            $description = 'Indemnisation financière directe';
        }
        
        $query = "UPDATE " . $this->typeReponse->getTableName() . " 
                  SET nom = :nom, 
                      description = :description, 
                      categorie = :categorie,
                      gouvernorat = :gouvernorat,
                      atelier_id = :atelier_id,
                      montant = :montant
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nom", $nom);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":categorie", $categorie);
        $stmt->bindParam(":gouvernorat", $gouvernorat);
        $stmt->bindParam(":atelier_id", $id_atelier);
        $stmt->bindParam(":montant", $montant);
        $stmt->execute();
        
        header("Location: index.php?action=type_reponses&update=1");
        exit();
    } catch(Exception $e) {
        $error = $e->getMessage();
        echo "Erreur: " . $error;
    }
}

    // Supprimer un type
    public function delete($id) {
        $query = "DELETE FROM " . $this->typeReponse->getTableName() . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        header("Location: index.php?action=type_reponses&delete=1");
        exit();
    }
}
?>