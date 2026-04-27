<?php
require_once __DIR__ . '/../models/Conge.php';
require_once __DIR__ . '/../config/database.php';

class CongeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Validation
    private function validate($data) {
        $errors = [];
        
        if (empty($data['date_debut'])) {
            $errors[] = 'La date de début est requise.';
        }
        if (empty($data['date_fin'])) {
            $errors[] = 'La date de fin est requise.';
        }
        if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
            if ($data['date_debut'] > $data['date_fin']) {
                $errors[] = 'La date de fin doit être après la date de début.';
            }
        }
        if (empty($data['type_conge'])) {
            $errors[] = 'Le type de congé est requis.';
        }
        if (empty($data['motif'])) {
            $errors[] = 'Le motif est requis.';
        }
        
        return $errors;
    }

    // Récupérer tous les congés (avec recherche et tri)
    public function all($filters = []) {
        $allowedSort = ['id_conge', 'date_debut', 'date_fin', 'type_conge', 'motif', 'statut', 'date_demande', 'id_employe'];
        $sort = $filters['sort'] ?? 'date_demande';
        $dir = strtoupper($filters['dir'] ?? 'DESC');
        $search = trim($filters['q'] ?? '');

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'date_demande';
        }
        if (!in_array($dir, ['ASC', 'DESC'], true)) {
            $dir = 'DESC';
        }

        $sql = "SELECT * FROM Conge";
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE CAST(id_conge AS CHAR) LIKE :q
                      OR type_conge LIKE :q
                      OR motif LIKE :q
                      OR statut LIKE :q
                      OR CAST(date_debut AS CHAR) LIKE :q
                      OR CAST(date_fin AS CHAR) LIKE :q
                      OR CAST(date_demande AS CHAR) LIKE :q
                      OR CAST(id_employe AS CHAR) LIKE :q";
            $params['q'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY $sort $dir";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Récupérer un congé par ID
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM Conge WHERE id_conge = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Sauvegarder (insert ou update)
    public function save($data, $id = null) {
        if ($id) {
            // Update
            $stmt = $this->db->prepare("
                UPDATE Conge 
                SET date_debut = :date_debut, 
                    date_fin = :date_fin, 
                    type_conge = :type_conge, 
                    motif = :motif, 
                    statut = :statut
                WHERE id_conge = :id
            ");
            return $stmt->execute([
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type_conge' => $data['type_conge'],
                'motif' => $data['motif'],
                'statut' => $data['statut'],
                'id' => $id
            ]);
        } else {
            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO Conge (date_debut, date_fin, type_conge, motif, statut, date_demande, id_employe) 
                VALUES (:date_debut, :date_fin, :type_conge, :motif, :statut, :date_demande, :id_employe)
            ");
            return $stmt->execute([
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type_conge' => $data['type_conge'],
                'motif' => $data['motif'],
                'statut' => $data['statut'],
                'date_demande' => date('Y-m-d'),
                'id_employe' => 1
            ]);
        }
    }

    // Supprimer un congé
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Conge WHERE id_conge = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Méthodes pour les vues
    public function index() {
        $congeFilters = [
            'q' => $_GET['q_conge'] ?? ($_GET['q'] ?? ''),
            'sort' => $_GET['sort_conge'] ?? ($_GET['sort'] ?? 'date_demande'),
            'dir' => $_GET['dir_conge'] ?? ($_GET['dir'] ?? 'DESC')
        ];
        $traitementFilters = [
            'q' => $_GET['q_traitement'] ?? '',
            'sort' => $_GET['sort_traitement'] ?? 'date_traitement',
            'dir' => $_GET['dir_traitement'] ?? 'DESC'
        ];

        $conges = $this->all($congeFilters);
        $traitements = $this->fetchTraitements($traitementFilters);
        $congeStats = $this->getCongeStatusStats($congeFilters);
        $traitementStats = $this->getTraitementDecisionStats($traitementFilters);
        require __DIR__ . '/../views/frontoffice/dashboard.php';
    }

    public function create() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validate($_POST);

            if (empty($errors)) {
                if ($this->save($_POST)) {
                    header('Location: index.php?action=index');
                    exit;
                }
                $errors[] = 'Impossible d\'enregistrer le congé.';
            }
        }

        require __DIR__ . '/../views/frontoffice/create.php';
    }

    public function edit($id) {
        $conge = $this->find($id);
        $errors = [];

        if (!$conge) {
            header('Location: index.php?action=adminIndex');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validate($_POST);

            if (empty($errors)) {
                if ($this->save($_POST, $id)) {
                    header('Location: index.php?action=adminIndex');
                    exit;
                }
                $errors[] = 'Impossible de mettre à jour le congé.';
            }
        }

        require __DIR__ . '/../views/backoffice/edit.php';
    }

    public function deleteAction($id) {
        $this->delete($id);
        header('Location: index.php?action=adminIndex');
        exit;
    }

    public function adminIndex() {
        $congeFilters = [
            'q' => $_GET['q_conge'] ?? ($_GET['q'] ?? ''),
            'sort' => $_GET['sort_conge'] ?? ($_GET['sort'] ?? 'date_demande'),
            'dir' => $_GET['dir_conge'] ?? ($_GET['dir'] ?? 'DESC')
        ];
        $traitementFilters = [
            'q' => $_GET['q_traitement'] ?? '',
            'sort' => $_GET['sort_traitement'] ?? 'date_traitement',
            'dir' => $_GET['dir_traitement'] ?? 'DESC'
        ];

        $conges = $this->all($congeFilters);
        $traitements = $this->fetchTraitements($traitementFilters);
        require __DIR__ . '/../views/frontoffice/dashboard.php';
    }

    public function exportPdf() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_demande',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $conges = $this->all($filters);

        $lines = ["Liste des conges"];
        $lines[] = "------------------------------";
        foreach ($conges as $c) {
            $lines[] = sprintf(
                "%s -> %s | %s | %s",
                $c['date_debut'],
                $c['date_fin'],
                $c['type_conge'],
                $c['statut']
            );
        }

        $this->outputSimplePdf('conges.pdf', $lines);
    }

    private function outputSimplePdf($fileName, $lines) {
        $y = 800;
        $text = "BT\n/F1 12 Tf\n50 $y Td\n";
        $first = true;

        foreach ($lines as $line) {
            $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
            if ($first) {
                $text .= "($safe) Tj\n";
                $first = false;
            } else {
                $text .= "0 -18 Td\n($safe) Tj\n";
            }
        }
        $text .= "ET";

        $pdfBody = "%PDF-1.4\n";
        $objects = [];

        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj\n";
        $objects[] = "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";
        $objects[] = "5 0 obj << /Length " . strlen($text) . " >> stream\n" . $text . "\nendstream endobj\n";

        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdfBody);
            $pdfBody .= $obj;
        }

        $xrefPos = strlen($pdfBody);
        $pdfBody .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdfBody .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdfBody .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdfBody .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdfBody .= "startxref\n$xrefPos\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($pdfBody));
        echo $pdfBody;
        exit;
    }

    private function fetchTraitements($filters = []) {
        $allowedSort = ['id_traitement', 'date_traitement', 'decision', 'commentaire', 'id_conge', 'type_conge'];
        $sort = $filters['sort'] ?? 'date_traitement';
        $dir = strtoupper($filters['dir'] ?? 'DESC');
        $search = trim($filters['q'] ?? '');

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'date_traitement';
        }
        if (!in_array($dir, ['ASC', 'DESC'], true)) {
            $dir = 'DESC';
        }

        $sql = "
            SELECT t.*, c.type_conge
            FROM TraitementConge t
            LEFT JOIN Conge c ON t.id_conge = c.id_conge
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE CAST(t.id_traitement AS CHAR) LIKE :q
                      OR CAST(t.date_traitement AS CHAR) LIKE :q
                      OR t.decision LIKE :q
                      OR t.commentaire LIKE :q
                      OR CAST(t.id_conge AS CHAR) LIKE :q
                      OR c.type_conge LIKE :q";
            $params['q'] = '%' . $search . '%';
        }

        $sortExpr = $sort === 'type_conge' ? 'c.type_conge' : "t.$sort";
        $sql .= " ORDER BY $sortExpr $dir";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function getCongeStatusStats($filters = []) {
        $search = trim($filters['q'] ?? '');
        $sql = "SELECT statut, COUNT(*) AS total FROM Conge";
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE CAST(id_conge AS CHAR) LIKE :q
                      OR type_conge LIKE :q
                      OR motif LIKE :q
                      OR statut LIKE :q
                      OR CAST(date_debut AS CHAR) LIKE :q
                      OR CAST(date_fin AS CHAR) LIKE :q
                      OR CAST(date_demande AS CHAR) LIKE :q
                      OR CAST(id_employe AS CHAR) LIKE :q";
            $params['q'] = '%' . $search . '%';
        }

        $sql .= " GROUP BY statut";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $total = 0;
        foreach ($rows as $r) {
            $total += (int)($r['total'] ?? 0);
        }

        $out = [];
        foreach ($rows as $r) {
            $count = (int)($r['total'] ?? 0);
            $status = (string)($r['statut'] ?? '');
            $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $out[] = ['key' => $status, 'count' => $count, 'pct' => $pct];
        }

        usort($out, function ($a, $b) { return ($b['count'] ?? 0) <=> ($a['count'] ?? 0); });
        return ['total' => $total, 'items' => $out];
    }

    private function getTraitementDecisionStats($filters = []) {
        $search = trim($filters['q'] ?? '');
        $sql = "
            SELECT t.decision AS decision, COUNT(*) AS total
            FROM TraitementConge t
            LEFT JOIN Conge c ON t.id_conge = c.id_conge
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE CAST(t.id_traitement AS CHAR) LIKE :q
                      OR CAST(t.date_traitement AS CHAR) LIKE :q
                      OR t.decision LIKE :q
                      OR t.commentaire LIKE :q
                      OR CAST(t.id_conge AS CHAR) LIKE :q
                      OR c.type_conge LIKE :q";
            $params['q'] = '%' . $search . '%';
        }

        $sql .= " GROUP BY t.decision";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $total = 0;
        foreach ($rows as $r) {
            $total += (int)($r['total'] ?? 0);
        }

        $out = [];
        foreach ($rows as $r) {
            $count = (int)($r['total'] ?? 0);
            $decision = (string)($r['decision'] ?? '');
            $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $out[] = ['key' => $decision, 'count' => $count, 'pct' => $pct];
        }

        usort($out, function ($a, $b) { return ($b['count'] ?? 0) <=> ($a['count'] ?? 0); });
        return ['total' => $total, 'items' => $out];
    }
}
?>