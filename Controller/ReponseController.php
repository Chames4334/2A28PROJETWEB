<?php
class ReponseController {
    private $conn;
    private $reponse;
    private $typeReponse;
    private $atelier;
    private $demande;

    public function __construct($db) {
        $this->conn = $db;
        $this->reponse = new Reponse($this->conn);
        $this->typeReponse = new TypeReponse($this->conn);
        $this->atelier = new Atelier($this->conn);
        $this->demande = new Demande($this->conn);
    }

    // Afficher toutes les réponses
    public function index() {
    // Optionally filter by demande_id
    $demandeFilter = isset($_GET['demande_id']) && is_numeric($_GET['demande_id']) ? (int)$_GET['demande_id'] : null;
    $query = "SELECT r.*, 
              t.nom as type_nom, 
              t.categorie as type_categorie,
              a.nom as atelier_nom,
              a.gouvernorat as atelier_gouv,
              CONCAT(d.prenom, ' ', d.nom) as client_nom
              FROM reponse_constat r 
              LEFT JOIN demande_constat d ON r.demande_id = d.id 
              LEFT JOIN type_reponse t ON r.type_reponse_id = t.id
              LEFT JOIN ateliers a ON r.id_atelier = a.id
              WHERE r.id IN (SELECT MAX(id) FROM reponse_constat GROUP BY demande_id) ";
    if ($demandeFilter) {
        $query .= " AND r.demande_id = :did ";
    }
    $query .= " ORDER BY r.created_at DESC";
    $stmt = $this->conn->prepare($query);
    if ($demandeFilter) $stmt->bindValue(':did', $demandeFilter, PDO::PARAM_INT);
    $stmt->execute();
    $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // If requested as partial (AJAX), include a lightweight partial view
    if (isset($_GET['partial']) && $_GET['partial']) {
        include ROOT_PATH . 'View/reponses/partial.php';
        return;
    }
    include ROOT_PATH . 'View/reponses/index.php';
}

    // Formulaire de création
    public function create() {
        $types = $this->getAllTypes();
        $ateliers = $this->getAllAteliers();
        $demandes = $this->getAllDemandes();
        include ROOT_PATH . 'View/reponses/create.php';
    }

    // Enregistrer une réponse
    public function store() {
        try {
            $demande_id = $_POST['demande_id'];
            
            $checkQuery = "SELECT id FROM reponse_constat WHERE demande_id = :demande_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(":demande_id", $demande_id);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                $error = "Une réponse existe déjà pour cette demande.";
                $types = $this->getAllTypes();
                $ateliers = $this->getAllAteliers();
                $demandes = $this->getAllDemandes();
                include ROOT_PATH . 'View/reponses/create.php';
                return;
            }
            
            $query = "INSERT INTO reponse_constat (demande_id, type_reponse_id, montant, id_atelier, message_admin) 
                      VALUES (:demande_id, :type_reponse_id, :montant, :id_atelier, :message_admin)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":demande_id", $_POST['demande_id']);
            $stmt->bindParam(":type_reponse_id", $_POST['type_reponse_id']);
            $stmt->bindParam(":montant", $_POST['montant']);
            $stmt->bindParam(":id_atelier", $_POST['id_atelier']);
            $stmt->bindParam(":message_admin", $_POST['message_admin']);
            $stmt->execute();
            
            $reponseId = $this->conn->lastInsertId();
            
            // Get demande details for notifications
            $demandeQuery = "SELECT d.*, t.nom as type_nom FROM demande_constat d 
                            LEFT JOIN reponse_constat r ON d.id = r.demande_id
                            LEFT JOIN type_reponse t ON r.type_reponse_id = t.id
                            WHERE d.id = :demande_id";
            $demandeStmt = $this->conn->prepare($demandeQuery);
            $demandeStmt->bindParam(":demande_id", $demande_id);
            $demandeStmt->execute();
            $demande = $demandeStmt->fetch(PDO::FETCH_ASSOC);
            
            // Send SMS notification
            require_once ROOT_PATH . 'Model/SmsService.php';
            $smsService = new SmsService($this->conn);
            $smsService->notifierReponse(
                $demande_id,
                $demande['telephone'],
                $demande['nom'],
                $_POST['type_reponse_id'],
                $_POST['montant']
            );
            
            // Send email notification
            require_once ROOT_PATH . 'Controller/NotificationController.php';
            $notification = new NotificationController($this->conn);
            $notification->notifierReponse(
                $demande_id,
                $demande['email'],
                $demande['nom'],
                $demande['prenom'],
                $demande['type_nom'] ?? 'Réponse',
                $_POST['montant']
            );
            
            // Update demande statut to en_cours
            $updateStatut = "UPDATE demande_constat SET statut = 'en_cours' WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateStatut);
            $updateStmt->bindParam(":id", $demande_id);
            $updateStmt->execute();
            
            header("Location: index.php?action=reponses&success=1");
            exit();
        } catch(Exception $e) {
            $error = $e->getMessage();
            $types = $this->getAllTypes();
            $ateliers = $this->getAllAteliers();
            $demandes = $this->getAllDemandes();
            include ROOT_PATH . 'View/reponses/create.php';
        }
    }

    // Formulaire d'édition
    public function edit($id) {
        $query = "SELECT * FROM reponse_constat WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $this->reponse->setId($row['id'])
                         ->setDemandeId($row['demande_id'])
                         ->setTypeReponseId($row['type_reponse_id'])
                         ->setMontant($row['montant'])
                         ->setIdAtelier($row['id_atelier'])
                         ->setMessageAdmin($row['message_admin'])
                         ->setStatutVoiture($row['statut_voiture'] ?? 'en_attente')
                         ->setTempsRestant($row['temps_restant'] ?? null);
            
            $types = $this->getAllTypes();
            $ateliers = $this->getAllAteliers();
            $demandes = $this->getAllDemandes();
            include ROOT_PATH . 'View/reponses/edit.php';
        } else {
            header("Location: index.php?action=reponses");
        }
    }

    // Mettre à jour
    public function update($id) {
        try {
            $query = "UPDATE reponse_constat 
                      SET demande_id = :demande_id, 
                          type_reponse_id = :type_reponse_id,
                          montant = :montant, 
                          id_atelier = :id_atelier, 
                          message_admin = :message_admin,
                          statut_voiture = :statut_voiture,
                          temps_restant = :temps_restant
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":demande_id", $_POST['demande_id']);
            $stmt->bindParam(":type_reponse_id", $_POST['type_reponse_id']);
            $stmt->bindParam(":montant", $_POST['montant']);
            $stmt->bindParam(":id_atelier", $_POST['id_atelier']);
            $stmt->bindParam(":message_admin", $_POST['message_admin']);
            $stmt->bindParam(":statut_voiture", $_POST['statut_voiture']);
            $stmt->bindParam(":temps_restant", $_POST['temps_restant']);
            $stmt->execute();
            
            header("Location: index.php?action=reponses&update=1");
            exit();
        } catch(Exception $e) {
            $error = $e->getMessage();
            $types = $this->getAllTypes();
            $ateliers = $this->getAllAteliers();
            $demandes = $this->getAllDemandes();
            include ROOT_PATH . 'View/reponses/edit.php';
        }
    }

    // Supprimer
    public function delete($id) {
        $query = "DELETE FROM reponse_constat WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        header("Location: index.php?action=reponses&delete=1");
        exit();
    }

    // Méthodes privées
    private function getAllTypes() {
        $query = "SELECT * FROM type_reponse ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAllAteliers() {
        $query = "SELECT * FROM ateliers ORDER BY nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAllDemandes() {
        $query = "SELECT * FROM demande_constat ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>