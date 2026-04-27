<?php

class TraitementConge {
    private $id_traitement;
    private $date_traitement;
    private $decision;
    private $commentaire;
    private $id_conge;

    // Constructeur
    public function __construct() {
    }

    // Setters
    public function setIdTraitement($id_traitement) {
        $this->id_traitement = (int)$id_traitement;
    }

    public function setDateTraitement($date_traitement) {
        $this->date_traitement = $date_traitement;
    }

    public function setDecision($decision) {
        $this->decision = $decision;
    }

    public function setCommentaire($commentaire) {
        $this->commentaire = $commentaire;
    }

    public function setIdConge($id_conge) {
        $this->id_conge = (int)$id_conge;
    }

    // Getters
    public function getIdTraitement() {
        return $this->id_traitement;
    }

    public function getDateTraitement() {
        return $this->date_traitement;
    }

    public function getDecision() {
        return $this->decision;
    }

    public function getCommentaire() {
        return $this->commentaire;
    }

    public function getIdConge() {
        return $this->id_conge;
    }
}
?>