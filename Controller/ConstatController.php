<?php
require_once __DIR__ . '/../Model/ConstatModel.php';
require_once __DIR__ . '/../Model/ReponseModel.php';
require_once __DIR__ . '/../Model/AtelierModel.php';

class ConstatController {
    private $model;

    public function __construct() {
        $this->model = new ConstatModel();
    }

    public function home() {
        require_once __DIR__ . '/../View/constat/home.php';
    }

    public function demande() {
        require_once __DIR__ . '/../View/constat/demande.php';
    }

    public function soumettre() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST);
            $_SESSION['success'] = "Déclaration enregistrée avec succès.";
            header('Location: index.php?action=home');
            exit;
        }
    }

    public function historique() {
        $constats = $this->model->getAll();
        require_once __DIR__ . '/../View/constat/historique.php';
    }

    public function liste() {
        $constats = $this->model->getAll();
        require_once __DIR__ . '/../View/constat/liste.php';
    }

    public function voirReponse() {
        $id = $_GET['id'] ?? 0;
        $constat = $this->model->getById($id);
        if (!$constat) die("Déclaration introuvable.");
        $reponseModel = new ReponseModel();
        $reponse = $reponseModel->getByDemandeId($id);
        require_once __DIR__ . '/../View/constat/reponse.php';
    }

    public function reponseForm() {
        $id = $_GET['id'] ?? 0;
        $constat = $this->model->getById($id);
        if (!$constat) die("Déclaration introuvable.");
        $reponseModel = new ReponseModel();
        $atelierModel = new AtelierModel();
        $reponse = $reponseModel->getByDemandeId($id);
        $ateliers = $atelierModel->getAll();
        require_once __DIR__ . '/../View/constat/reponse_form.php';
    }

    public function reponseSubmit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reponseModel = new ReponseModel();
            $demande_id = $_POST['demande_id'];
            $type_reponse = $_POST['type_reponse'];
            $montant = ($type_reponse === 'remboursement') ? $_POST['montant'] : null;
            $id_atelier = ($type_reponse === 'atelier') ? $_POST['id_atelier'] : null;
            $message_admin = $_POST['message_admin'] ?? '';
            $reponseModel->createOrUpdate($demande_id, $type_reponse, $montant, $id_atelier, $message_admin);
            $this->model->updateStatut($demande_id, 'traite');
            $_SESSION['success'] = "Réponse enregistrée.";
            header('Location: index.php?action=liste');
            exit;
        }
    }
}
?>