<?php
require_once __DIR__ . '/../config/database.php';

class Conge {
    private $db;
    private $id_conge;
    private $date_debut;
    private $date_fin;
    private $type_conge;
    private $motif;
    private $statut;
    private $date_demande;
    private $id_employe;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function setData(array $data) {
        $this->date_debut = trim($data['date_debut'] ?? '');
        $this->date_fin = trim($data['date_fin'] ?? '');
        $this->type_conge = trim($data['type_conge'] ?? '');
        $this->motif = trim($data['motif'] ?? '');
        $this->statut = trim($data['statut'] ?? 'en_attente');
        $this->date_demande = trim($data['date_demande'] ?? date('Y-m-d'));
        $this->id_employe = trim($data['id_employe'] ?? '');
    }

    public function validate() {
        $errors = [];

        if ($this->date_debut === '') {
            $errors[] = 'La date de début est requise.';
        }

        if ($this->date_fin === '') {
            $errors[] = 'La date de fin est requise.';
        }

        if ($this->date_debut !== '' && $this->date_fin !== '') {
            $start = strtotime($this->date_debut);
            $end = strtotime($this->date_fin);
            if ($start === false || $end === false) {
                $errors[] = 'Le format des dates est invalide. Utilisez AAAA-MM-JJ.';
            } elseif ($start > $end) {
                $errors[] = 'La date de début doit être antérieure à la date de fin.';
            }
        }

        if ($this->type_conge === '') {
            $errors[] = 'Le type de congé est requis.';
        }

        if ($this->motif === '') {
            $errors[] = 'Le motif est requis.';
        }

        if ($this->id_employe === '' || !ctype_digit($this->id_employe)) {
            $errors[] = 'L\'ID employé est requis et doit être un nombre.';
        }

        return $errors;
    }

    public function all() {
        $stmt = $this->db->query('SELECT * FROM Conge ORDER BY date_demande DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM Conge WHERE id_conge = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function save() {
        if (!empty($this->id_conge)) {
            $stmt = $this->db->prepare('UPDATE Conge SET date_debut = :date_debut, date_fin = :date_fin, type_conge = :type_conge, motif = :motif, statut = :statut, date_demande = :date_demande, id_employe = :id_employe WHERE id_conge = :id_conge');
            return $stmt->execute([
                'date_debut' => $this->date_debut,
                'date_fin' => $this->date_fin,
                'type_conge' => $this->type_conge,
                'motif' => $this->motif,
                'statut' => $this->statut,
                'date_demande' => $this->date_demande,
                'id_employe' => $this->id_employe,
                'id_conge' => $this->id_conge,
            ]);
        }

        $stmt = $this->db->prepare('INSERT INTO Conge (date_debut, date_fin, type_conge, motif, statut, date_demande, id_employe) VALUES (:date_debut, :date_fin, :type_conge, :motif, :statut, :date_demande, :id_employe)');
        return $stmt->execute([
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'type_conge' => $this->type_conge,
            'motif' => $this->motif,
            'statut' => $this->statut,
            'date_demande' => $this->date_demande,
            'id_employe' => $this->id_employe,
        ]);
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare('DELETE FROM Conge WHERE id_conge = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function setId(int $id) {
        $this->id_conge = $id;
    }
}
