<?php
class DemandeController {
    private $conn;
    private $demande;

    public function __construct($db) {
        $this->conn = $db;
        $this->demande = new Demande($this->conn);
    }

    // READ - Toutes les demandes
    public function index() {
        $query = "SELECT * FROM " . $this->demande->getTableName() . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include ROOT_PATH . 'View/demandes/index.php';
    }

    // CREATE - Formulaire
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            include ROOT_PATH . 'View/demandes/create.php';
        }
    }

    // CREATE - Enregistrer
   public function store() {
    try {
        $this->demande->setNom($_POST['nom'])
                     ->setPrenom($_POST['prenom'])
                     ->setEmail($_POST['email'])
                     ->setTelephone($_POST['telephone'])
                     ->setLieuAccident($_POST['lieu_accident'])
                     ->setDateAccident($_POST['date_accident'])
                     ->setDescription($_POST['description']);
        
        $query = "INSERT INTO " . $this->demande->getTableName() . "
                  SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                      lieu_accident=:lieu_accident, date_accident=:date_accident,
                      description=:description, statut='soumis'";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nom", $this->demande->getNom());
        $stmt->bindParam(":prenom", $this->demande->getPrenom());
        $stmt->bindParam(":email", $this->demande->getEmail());
        $stmt->bindParam(":telephone", $this->demande->getTelephone());
        $stmt->bindParam(":lieu_accident", $this->demande->getLieuAccident());
        $stmt->bindParam(":date_accident", $this->demande->getDateAccident());
        $stmt->bindParam(":description", $this->demande->getDescription());
        
        if ($stmt->execute()) {
            $demandeId = $this->conn->lastInsertId();
            
            // Send confirmation email to user
            require_once ROOT_PATH . 'Controller/NotificationController.php';
            $notification = new NotificationController($this->conn);
            $notification->notifierConfirmationDemande(
                $demandeId,
                $this->demande->getEmail(),
                $this->demande->getNom(),
                $this->demande->getPrenom()
            );
            
            // If the form included a Type (atelier or remboursement), create a reponse_constat entry
            try {
                if (!empty($_POST['type_mode'])) {
                    $typeMode = $_POST['type_mode'];
                    $typeReponseId = $_POST['type_reponse_id'] ?? null;
                    $idAtelier = $_POST['id_atelier'] ?? null;
                    $montant = $_POST['montant_client'] ?? null;

                    // If no explicit type_reponse_id provided, try to pick a matching one
                    if (empty($typeReponseId)) {
                        $q = "SELECT id FROM type_reponse WHERE categorie = :cat LIMIT 1";
                        $s = $this->conn->prepare($q);
                        $s->bindParam(':cat', $typeMode);
                        $s->execute();
                        $found = $s->fetch(PDO::FETCH_ASSOC);
                        if ($found) $typeReponseId = $found['id'];
                    }

                    if ($typeReponseId || $idAtelier || $montant) {
                        $ins = "INSERT INTO reponse_constat (demande_id, type_reponse_id, montant, id_atelier, message_admin) VALUES (:demande_id, :type_reponse_id, :montant, :id_atelier, :message_admin)";
                        $s2 = $this->conn->prepare($ins);
                        $s2->bindValue(':demande_id', (int)$demandeId, PDO::PARAM_INT);
                        if (!empty($typeReponseId)) {
                            $s2->bindValue(':type_reponse_id', (int)$typeReponseId, PDO::PARAM_INT);
                        } else {
                            $s2->bindValue(':type_reponse_id', null, PDO::PARAM_NULL);
                        }
                        if ($montant !== null && $montant !== '') {
                            $s2->bindValue(':montant', $montant);
                        } else {
                            $s2->bindValue(':montant', null, PDO::PARAM_NULL);
                        }
                        if (!empty($idAtelier)) {
                            $s2->bindValue(':id_atelier', (int)$idAtelier, PDO::PARAM_INT);
                        } else {
                            $s2->bindValue(':id_atelier', null, PDO::PARAM_NULL);
                        }
                        $msgAdmin = 'Réponse initiale client via formulaire';
                        $s2->bindValue(':message_admin', $msgAdmin, PDO::PARAM_STR);
                        $s2->execute();
                    }
                }
            } catch (Exception $e) {
                // ignore insertion error of response, notification handled elsewhere
            }
            
            header("Location: index.php?action=demandes&success=1");
            exit();
        }
    } catch(Exception $e) {
        $error = $e->getMessage();
        echo "Erreur: " . $error;
        include ROOT_PATH . 'View/demandes/create.php';
    }
}

    // READ - Une demande
    public function show($id) {
    $this->demande->setId($id);
    $query = "SELECT * FROM " . $this->demande->getTableName() . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$this->demande->getId()]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $this->demande->setNom($row['nom'])
                     ->setPrenom($row['prenom'])
                     ->setEmail($row['email'])
                     ->setTelephone($row['telephone'])
                     ->setLieuAccident($row['lieu_accident'])
                     ->setDateAccident($row['date_accident'])
                     ->setDescription($row['description'])
                     ->setStatut($row['statut'])
                     ->setCreatedAt($row['created_at']);
        // Load available types and ateliers for quick response (Types) modal
        $types = [];
        $ateliers = [];
        try {
            $tq = "SELECT * FROM type_reponse ORDER BY id";
            $ts = $this->conn->prepare($tq);
            $ts->execute();
            $types = $ts->fetchAll(PDO::FETCH_ASSOC);

            $aq = "SELECT * FROM ateliers ORDER BY nom";
            $as = $this->conn->prepare($aq);
            $as->execute();
            $ateliers = $as->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // ignore
        }

        // Load the latest response for this demande (if any)
        $reponse = null;
        try {
            $rq = "SELECT r.*, t.nom as type_nom, t.categorie as type_categorie, a.nom as atelier_nom, a.gouvernorat as atelier_gouv FROM reponse_constat r LEFT JOIN type_reponse t ON r.type_reponse_id = t.id LEFT JOIN ateliers a ON r.id_atelier = a.id WHERE r.demande_id = :did ORDER BY r.id DESC LIMIT 1";
            $rst = $this->conn->prepare($rq);
            $rst->bindParam(':did', $id);
            $rst->execute();
            $reponse = $rst->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // ignore
        }

        include ROOT_PATH . 'View/demandes/show.php';
    } else {
        header("Location: index.php?action=demandes");
    }
}

public function edit($id) {
    $this->demande->setId($id);
    $query = "SELECT * FROM " . $this->demande->getTableName() . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$this->demande->getId()]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        $this->demande->setNom($row['nom'])
                     ->setPrenom($row['prenom'])
                     ->setEmail($row['email'])
                     ->setTelephone($row['telephone'])
                     ->setLieuAccident($row['lieu_accident'])
                     ->setDateAccident($row['date_accident'])
                     ->setDescription($row['description'])
                     ->setStatut($row['statut']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            include ROOT_PATH . 'View/demandes/edit.php';
        }
    } else {
        header("Location: index.php?action=demandes");
    }
}
    public function update($id) {
        try {
            $this->demande->setId($id)
                         ->setNom($_POST['nom'])
                         ->setPrenom($_POST['prenom'])
                         ->setEmail($_POST['email'])
                         ->setTelephone($_POST['telephone'])
                         ->setLieuAccident($_POST['lieu_accident'])
                         ->setDateAccident($_POST['date_accident'])
                         ->setDescription($_POST['description'])
                         ->setStatut($_POST['statut']);
            
            $query = "UPDATE " . $this->demande->getTableName() . "
                      SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                          lieu_accident=:lieu_accident, date_accident=:date_accident,
                          description=:description, statut=:statut
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":id", $this->demande->getId());
            $stmt->bindParam(":nom", $this->demande->getNom());
            $stmt->bindParam(":prenom", $this->demande->getPrenom());
            $stmt->bindParam(":email", $this->demande->getEmail());
            $stmt->bindParam(":telephone", $this->demande->getTelephone());
            $stmt->bindParam(":lieu_accident", $this->demande->getLieuAccident());
            $stmt->bindParam(":date_accident", $this->demande->getDateAccident());
            $stmt->bindParam(":description", $this->demande->getDescription());
            $stmt->bindParam(":statut", $this->demande->getStatut());
            
            if ($stmt->execute()) {
                header("Location: index.php?action=demandes&update=1");
                exit();
            }
        } catch(Exception $e) {
            $error = $e->getMessage();
            include ROOT_PATH . 'View/demandes/edit.php';
        }
    }

    /**
     * Store a demande quickly from the Types modal
     */
    public function storeFromType() {
        try {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $telephone = $_POST['telephone'] ?? '';
            $lieu = $_POST['lieu_accident'] ?? '';
            $date_accident = $_POST['date_accident'] ?? null;
            $description = $_POST['description'] ?? '';

            $query = "INSERT INTO " . $this->demande->getTableName() . "
                      SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone,
                          lieu_accident=:lieu_accident, date_accident=:date_accident,
                          description=:description, statut='soumis'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':lieu_accident', $lieu);
            $stmt->bindParam(':date_accident', $date_accident);
            $stmt->bindParam(':description', $description);

            if ($stmt->execute()) {
                $demandeId = $this->conn->lastInsertId();
                // Optionally send confirmation email
                try {
                    require_once ROOT_PATH . 'Controller/NotificationController.php';
                    $notification = new NotificationController($this->conn);
                    $notification->notifierConfirmationDemande($demandeId, $email, $nom, $prenom);
                } catch (Exception $e) {
                    // ignore email errors here; session notif handled in NotificationController
                }
                header('Location: index.php?action=demandes&success=1');
                exit();
            } else {
                $_SESSION['notif_msg'] = '❌ Erreur lors de la création de la demande';
                header('Location: index.php?action=demandes');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['notif_msg'] = '❌ Exception: ' . $e->getMessage();
            header('Location: index.php?action=demandes');
            exit();
        }
    }

    // DELETE
    public function delete($id) {
        $this->demande->setId($id);
        $query = "DELETE FROM " . $this->demande->getTableName() . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->demande->getId()]);
        header("Location: index.php?action=demandes&delete=1");
        exit();
    }

    // SEARCH
    public function search() {
        $keyword = $_GET['keyword'] ?? '';
        $query = "SELECT * FROM " . $this->demande->getTableName() . "
                  WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? 
                     OR lieu_accident LIKE ? OR description LIKE ?
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->execute([$keyword, $keyword, $keyword, $keyword, $keyword]);
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include ROOT_PATH . 'View/demandes/index.php';
    }
}
?>