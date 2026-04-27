<?php

class Conge {
    private $id_conge;
    private $date_debut;
    private $date_fin;
    private $type_conge;
    private $motif;
    private $statut;
    private $date_demande;
    private $id_employe;

    // Constructeur
    public function __construct() {
    }

    // Setters
    public function setIdConge($id_conge) {
        $this->id_conge = (int)$id_conge;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }

    public function setTypeConge($type_conge) {
        $this->type_conge = $type_conge;
    }

    public function setMotif($motif) {
        $this->motif = $motif;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function setDateDemande($date_demande) {
        $this->date_demande = $date_demande;
    }

    public function setIdEmploye($id_employe) {
        $this->id_employe = (int)$id_employe;
    }

    // Getters
    public function getIdConge() {
        return $this->id_conge;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function getDateFin() {
        return $this->date_fin;
    }

    public function getTypeConge() {
        return $this->type_conge;
    }

    public function getMotif() {
        return $this->motif;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getDateDemande() {
        return $this->date_demande;
    }

    public function getIdEmploye() {
        return $this->id_employe;
    }
}
?>