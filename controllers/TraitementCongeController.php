<?php
require_once __DIR__ . '/../models/TraitementConge.php';
require_once __DIR__ . '/../config/database.php';

class TraitementCongeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Validation
    private function validate($data) {
        $errors = [];
        
        if (empty($data['id_conge'])) {
            $errors[] = 'Un congé associé est requis.';
        }
        if (empty($data['date_traitement'])) {
            $errors[] = 'La date de traitement est requise.';
        }
        if (empty($data['decision'])) {
            $errors[] = 'La décision est requise.';
        }
        
        return $errors;
    }

    // Récupérer tous les traitements (avec recherche et tri)
    public function all($filters = []) {
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

    // Récupérer un traitement par ID
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM TraitementConge WHERE id_traitement = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Récupérer tous les congés pour le select
    public function getConges() {
        $stmt = $this->db->prepare("SELECT id_conge, date_debut, date_fin FROM Conge ORDER BY id_conge DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Sauvegarder (insert ou update)
    public function save($data, $id = null) {
        if ($id) {
            // Update
            $stmt = $this->db->prepare("
                UPDATE TraitementConge 
                SET date_traitement = :date_traitement, 
                    decision = :decision, 
                    commentaire = :commentaire, 
                    id_conge = :id_conge
                WHERE id_traitement = :id
            ");
            return $stmt->execute([
                'date_traitement' => $data['date_traitement'],
                'decision' => $data['decision'],
                'commentaire' => $data['commentaire'],
                'id_conge' => $data['id_conge'],
                'id' => $id
            ]);
        } else {
            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO TraitementConge (date_traitement, decision, commentaire, id_conge) 
                VALUES (:date_traitement, :decision, :commentaire, :id_conge)
            ");
            return $stmt->execute([
                'date_traitement' => $data['date_traitement'],
                'decision' => $data['decision'],
                'commentaire' => $data['commentaire'],
                'id_conge' => $data['id_conge']
            ]);
        }
    }

    // Supprimer un traitement
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM TraitementConge WHERE id_traitement = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Méthodes pour les vues
    public function index() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_traitement',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $traitements = $this->all($filters);
        require __DIR__ . '/../views/frontoffice/traitements_list.php';
    }

    public function create() {
        $conges = $this->getConges();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validate($_POST);

            if (empty($errors)) {
                if ($this->save($_POST)) {
                    header('Location: index.php?action=traitementIndex');
                    exit;
                }
                $errors[] = 'Impossible d\'enregistrer le traitement.';
            }
        }

        require __DIR__ . '/../views/frontoffice/traitements_create.php';
    }

    public function edit($id) {
        $traitement = $this->find($id);
        $conges = $this->getConges();
        $errors = [];

        if (!$traitement) {
            header('Location: index.php?action=traitementAdminIndex');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validate($_POST);

            if (empty($errors)) {
                if ($this->save($_POST, $id)) {
                    // Rediriger vers la page d'où on vient
                    $redirect = isset($_GET['from']) && $_GET['from'] === 'admin' ? 'traitementAdminIndex' : 'traitementIndex';
                    header('Location: index.php?action=' . $redirect);
                    exit;
                }
                $errors[] = 'Impossible de mettre à jour le traitement.';
            }
        }

        require __DIR__ . '/../views/backoffice/traitements_edit.php';
    }

    public function deleteAction($id) {
        $this->delete($id);
        // Rediriger vers la page d'où on vient
        $redirect = isset($_GET['from']) && $_GET['from'] === 'admin' ? 'traitementAdminIndex' : 'traitementIndex';
        header('Location: index.php?action=' . $redirect);
        exit;
    }

    public function adminIndex() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_traitement',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $traitements = $this->all($filters);
        require __DIR__ . '/../views/backoffice/traitements_list.php';
    }

    public function exportPdf() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_traitement',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $traitements = $this->all($filters);

        $lines = ["Liste des traitements"];
        $lines[] = "------------------------------";
        foreach ($traitements as $t) {
            $lines[] = sprintf(
                "%s | %s | %s",
                $t['type_conge'] ?? '',
                $t['date_traitement'],
                $t['decision']
            );
        }

        $this->outputSimplePdf('traitements.pdf', $lines);
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
}
?>