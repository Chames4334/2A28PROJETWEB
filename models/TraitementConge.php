<?php
require_once __DIR__ . '/../config/database.php';

class TraitementConge {
    private $db;
    private $id_traitement;
    private $date_traitement;
    private $decision;
    private $commentaire;
    private $id_conge;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function setData(array $data) {
        $this->date_traitement = trim($data['date_traitement'] ?? date('Y-m-d'));
        $this->decision = trim($data['decision'] ?? '');
        $this->commentaire = trim($data['commentaire'] ?? '');
        $this->id_conge = (int)($data['id_conge'] ?? 0);
    }

    public function validate() {
        $errors = [];

        if ($this->date_traitement === '') {
            $errors[] = 'La date de traitement est requise.';
        } else {
            $timestamp = strtotime($this->date_traitement);
            if ($timestamp === false) {
                $errors[] = 'Le format de la date est invalide (utilisez AAAA-MM-JJ).';
            }
        }

        if ($this->decision === '') {
            $errors[] = 'La décision est requise.';
        } elseif (!in_array($this->decision, ['approuvé', 'refusé', 'en_attente'])) {
            $errors[] = 'La décision doit être approuvé, refusé ou en_attente.';
        }

        if ($this->id_conge <= 0) {
            $errors[] = 'Le congé est requis.';
        }

        return $errors;
    }

    public function all() {
        $stmt = $this->db->query('
            SELECT tc.*, c.type_conge, c.date_debut, c.date_fin, c.id_employe
            FROM TraitementConge tc
            LEFT JOIN Conge c ON tc.id_conge = c.id_conge
            ORDER BY tc.date_traitement DESC
        ');
        return $stmt->fetchAll();
    }

    public function find(int $id) {
        $stmt = $this->db->prepare('
            SELECT tc.*, c.type_conge, c.date_debut, c.date_fin, c.id_employe, c.motif, c.statut
            FROM TraitementConge tc
            LEFT JOIN Conge c ON tc.id_conge = c.id_conge
            WHERE tc.id_traitement = :id
        ');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findByCongo(int $congeId) {
        $stmt = $this->db->prepare('
            SELECT tc.* FROM TraitementConge tc
            WHERE tc.id_conge = :id_conge
            ORDER BY tc.date_traitement DESC
        ');
        $stmt->execute(['id_conge' => $congeId]);
        return $stmt->fetchAll();
    }

    public function save() {
        if (!empty($this->id_traitement)) {
            $stmt = $this->db->prepare('
                UPDATE TraitementConge 
                SET date_traitement = :date_traitement, decision = :decision, commentaire = :commentaire, id_conge = :id_conge
                WHERE id_traitement = :id_traitement
            ');
            return $stmt->execute([
                'date_traitement' => $this->date_traitement,
                'decision' => $this->decision,
                'commentaire' => $this->commentaire,
                'id_conge' => $this->id_conge,
                'id_traitement' => $this->id_traitement,
            ]);
        }

        $stmt = $this->db->prepare('
            INSERT INTO TraitementConge (date_traitement, decision, commentaire, id_conge)
            VALUES (:date_traitement, :decision, :commentaire, :id_conge)
        ');
        return $stmt->execute([
            'date_traitement' => $this->date_traitement,
            'decision' => $this->decision,
            'commentaire' => $this->commentaire,
            'id_conge' => $this->id_conge,
        ]);
    }

    public function delete(int $id) {
        $stmt = $this->db->prepare('DELETE FROM TraitementConge WHERE id_traitement = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function getConges() {
        $stmt = $this->db->query('
            SELECT id_conge, type_conge, date_debut, date_fin, id_employe
            FROM Conge
            ORDER BY date_debut DESC
        ');
        return $stmt->fetchAll();
    }

    public function setId(int $id) {
        $this->id_traitement = $id;
    }
}
