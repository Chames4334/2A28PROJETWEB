<?php
require_once __DIR__ . '/../models/Conge.php';
require_once __DIR__ . '/../config/database.php';

class CongeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Calcul des jours ouvrés (sans week-ends)
    private function getWorkingDays($startDate, $endDate) {
        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $days = 0;
        foreach ($period as $dt) {
            if ($dt->format('N') < 6) {
                $days++;
            }
        }
        return $days;
    }

    // Récupérer les informations de l'employé et son solde
    public function getEmployeInfo($id_employe) {
        $stmt = $this->db->prepare("SELECT * FROM Employe WHERE id_employe = :id");
        $stmt->execute(['id' => $id_employe]);
        $employe = $stmt->fetch();
        
        if (!$employe) {
            return ['solde_total' => 30, 'nom' => 'Inconnu', 'prenom' => 'Employe'];
        }

        $stmt = $this->db->prepare("SELECT date_debut, date_fin FROM Conge WHERE id_employe = :id AND statut = 'approuvé'");
        $stmt->execute(['id' => $id_employe]);
        $congesApprouves = $stmt->fetchAll();
        
        $joursPris = 0;
        foreach ($congesApprouves as $c) {
            $joursPris += $this->getWorkingDays($c['date_debut'], $c['date_fin']);
        }

        $employe['jours_pris'] = $joursPris;
        $employe['solde_restant'] = $employe['solde_total'] - $joursPris;
        return $employe;
    }

    // Validation complète incluant les champs de traitement
    private function validate($data, $isUpdate = false) {
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
        
        // Validation des champs de traitement (optionnelle)
        if (isset($data['decision']) && !empty($data['decision'])) {
            if (empty($data['date_traitement'])) {
                $errors[] = 'La date de traitement est requise lorsqu\'une décision est prise.';
            }
        }

        // Détection des conflits
        if (empty($errors)) {
            $id_employe = $data['id_employe'] ?? 1; // Default
            $debut = $data['date_debut'];
            $fin = $data['date_fin'];
            
            // 1. Conflit personnel (chevauchement)
            $sqlConflict = "SELECT id_conge FROM Conge WHERE id_employe = :id AND statut != 'refusé' AND date_debut <= :fin AND date_fin >= :debut";
            if ($isUpdate && isset($_GET['id'])) {
                $sqlConflict .= " AND id_conge != " . (int)$_GET['id'];
            }
            $stmtConf = $this->db->prepare($sqlConflict);
            $stmtConf->execute(['id' => $id_employe, 'fin' => $fin, 'debut' => $debut]);
            if ($stmtConf->fetch()) {
                $errors[] = 'Conflit : Vous avez déjà un congé (en attente ou approuvé) sur cette période.';
            }

            // 2. Conflit d'équipe (Max 2 absents simultanément)
            $sqlTeam = "SELECT COUNT(DISTINCT id_employe) as absents FROM Conge WHERE statut = 'approuvé' AND id_employe != :id AND date_debut <= :fin AND date_fin >= :debut";
            $stmtTeam = $this->db->prepare($sqlTeam);
            $stmtTeam->execute(['id' => $id_employe, 'fin' => $fin, 'debut' => $debut]);
            $team = $stmtTeam->fetch();
            if ($team && $team['absents'] >= 2) {
                $errors[] = 'Conflit d\'équipe : La limite de 2 employés absents en même temps est déjà atteinte sur cette période.';
            }
        }
        
        return $errors;
    }

    // Récupérer tous les congés (avec recherche et tri)
    public function all($filters = []) {
        $allowedSort = ['id_conge', 'date_debut', 'date_fin', 'type_conge', 'motif', 'statut', 'date_demande', 'id_employe', 'date_traitement', 'decision'];
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
                      OR CAST(id_employe AS CHAR) LIKE :q
                      OR CAST(date_traitement AS CHAR) LIKE :q
                      OR decision LIKE :q";
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
        $id_employe = $data['id_employe'] ?? 1;
        $statut = $data['statut'] ?? 'en_attente';
        $decision = $data['decision'] ?? null;
        $date_traitement = !empty($data['date_traitement']) ? $data['date_traitement'] : null;
        $commentaire_traitement = !empty($data['commentaire_traitement']) ? $data['commentaire_traitement'] : null;

        if (!$id && in_array($data['type_conge'], ['Congé payé', 'Congé maladie'])) {
            // Tentative de validation automatique
            $employeInfo = $this->getEmployeInfo($id_employe);
            $joursDemandes = $this->getWorkingDays($data['date_debut'], $data['date_fin']);
            
            if ($joursDemandes <= $employeInfo['solde_restant']) {
                $statut = 'approuvé';
                $decision = 'approuvé';
                $date_traitement = date('Y-m-d');
                $commentaire_traitement = 'Validation automatique (solde suffisant et aucun conflit).';
            } else {
                $statut = 'refusé';
                $decision = 'refusé';
                $date_traitement = date('Y-m-d');
                $commentaire_traitement = 'Refus automatique (solde insuffisant).';
            }
        }

        if ($id) {
            $stmt = $this->db->prepare("
                UPDATE Conge 
                SET date_debut = :date_debut, 
                    date_fin = :date_fin, 
                    type_conge = :type_conge, 
                    motif = :motif, 
                    statut = :statut,
                    date_traitement = :date_traitement,
                    decision = :decision,
                    commentaire_traitement = :commentaire_traitement
                WHERE id_conge = :id
            ");
            return $stmt->execute([
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type_conge' => $data['type_conge'],
                'motif' => $data['motif'],
                'statut' => $data['statut'] ?? $statut, // Fallback on original if provided
                'date_traitement' => $date_traitement,
                'decision' => $decision,
                'commentaire_traitement' => $commentaire_traitement,
                'id' => $id
            ]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO Conge (date_debut, date_fin, type_conge, motif, statut, date_demande, id_employe, date_traitement, decision, commentaire_traitement) 
                VALUES (:date_debut, :date_fin, :type_conge, :motif, :statut, :date_demande, :id_employe, :date_traitement, :decision, :commentaire_traitement)
            ");
            return $stmt->execute([
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin'],
                'type_conge' => $data['type_conge'],
                'motif' => $data['motif'],
                'statut' => $statut,
                'date_demande' => date('Y-m-d'),
                'id_employe' => $id_employe,
                'date_traitement' => $date_traitement,
                'decision' => $decision,
                'commentaire_traitement' => $commentaire_traitement
            ]);
        }
    }

    // Supprimer un congé
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Conge WHERE id_conge = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Mettre à jour seulement le traitement
    public function updateTraitement($id, $traitementData) {
        $stmt = $this->db->prepare("
            UPDATE Conge 
            SET date_traitement = :date_traitement,
                decision = :decision,
                commentaire_traitement = :commentaire_traitement,
                statut = :decision
            WHERE id_conge = :id
        ");
        return $stmt->execute([
            'date_traitement' => $traitementData['date_traitement'],
            'decision' => $traitementData['decision'],
            'commentaire_traitement' => $traitementData['commentaire_traitement'],
            'id' => $id
        ]);
    }

    // Obtenir les statistiques
    public function getStats() {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                SUM(CASE WHEN statut = 'approuvé' THEN 1 ELSE 0 END) as approuve,
                SUM(CASE WHEN statut = 'refusé' THEN 1 ELSE 0 END) as refuse,
                SUM(CASE WHEN date_traitement IS NOT NULL THEN 1 ELSE 0 END) as traites
            FROM Conge
        ");
        $stmt->execute();
        return $stmt->fetch();
    }

    // Méthodes pour les vues
    public function index() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_demande',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $conges = $this->all($filters);
        $stats = $this->getStats();
        $employeInfo = $this->getEmployeInfo(1); // Employé par défaut pour le frontoffice
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

        // Calcul intelligent de la charge et suggestions
        $stmt = $this->db->prepare("SELECT date_debut, date_fin FROM Conge WHERE date_fin >= CURDATE() AND statut != 'refusé'");
        $stmt->execute();
        $congesFuturs = $stmt->fetchAll();

        $workload = [];
        $today = new DateTime();
        // Initialiser la charge pour les 60 prochains jours
        for ($i = 0; $i < 60; $i++) {
            $d = clone $today;
            $d->modify("+$i days");
            $workload[$d->format('Y-m-d')] = 0;
        }

        // Remplir la charge
        foreach ($congesFuturs as $c) {
            $start = new DateTime($c['date_debut']);
            $end = new DateTime($c['date_fin']);
            for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
                $dateStr = $d->format('Y-m-d');
                if (isset($workload[$dateStr])) {
                    $workload[$dateStr]++;
                }
            }
        }

        // Trouver des suggestions (blocs de 5 jours ouvrés consécutifs sans absence)
        $suggestions = [];
        $currentBlockStart = null;
        $consecutiveDays = 0;

        for ($i = 1; $i < 60; $i++) { // On commence demain
            $d = clone $today;
            $d->modify("+$i days");
            $dateStr = $d->format('Y-m-d');
            $dayOfWeek = (int)$d->format('N'); // 1 = Lundi, 7 = Dimanche

            if ($dayOfWeek <= 5 && $workload[$dateStr] === 0) {
                if ($consecutiveDays === 0) {
                    $currentBlockStart = clone $d;
                }
                $consecutiveDays++;
                
                if ($consecutiveDays === 5) {
                    $endBlock = clone $currentBlockStart;
                    $endBlock->modify('+4 days');
                    $suggestions[] = [
                        'debut' => $currentBlockStart->format('Y-m-d'),
                        'fin' => $endBlock->format('Y-m-d'),
                        'label' => 'Semaine du ' . $currentBlockStart->format('d/m/Y')
                    ];
                    $consecutiveDays = 0; // Réinitialiser pour trouver d'autres blocs
                    if (count($suggestions) >= 3) break; // 3 suggestions max
                }
            } else if ($dayOfWeek <= 5) {
                $consecutiveDays = 0;
            }
        }

        $workloadJson = json_encode($workload);

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
            $errors = $this->validate($_POST, true);

            if (empty($errors)) {
                if ($this->save($_POST, $id)) {
                    $redirect = isset($_GET['from']) && $_GET['from'] === 'admin' ? 'adminIndex' : 'index';
                    header('Location: index.php?action=' . $redirect);
                    exit;
                }
                $errors[] = 'Impossible de mettre à jour le congé.';
            }
        }

        require __DIR__ . '/../views/backoffice/edit.php';
    }

    public function editTraitement($id) {
        $conge = $this->find($id);
        $errors = [];

        if (!$conge) {
            header('Location: index.php?action=adminIndex');
            exit;
        }

        // Vérification du solde
        $employeInfo = $this->getEmployeInfo($conge['id_employe']);
        $joursDemandes = $this->getWorkingDays($conge['date_debut'], $conge['date_fin']);
        $soldeInsuffisant = ($joursDemandes > $employeInfo['solde_restant']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
            $decision = $_POST['decision'] ?? 'en_attente';
            
            // Sécurité : Forcer le refus si solde insuffisant, peu importe le POST
            if ($soldeInsuffisant && $decision === 'approuvé') {
                $decision = 'refusé';
                $_POST['commentaire_traitement'] = ($_POST['commentaire_traitement'] ?? '') . " (Refus automatique : Solde insuffisant)";
            }

            $traitementData = [
                'date_traitement' => $_POST['date_traitement'] ?? date('Y-m-d'),
                'decision' => $decision,
                'commentaire_traitement' => $_POST['commentaire_traitement'] ?? ''
            ];
            
            if ($this->updateTraitement($id, $traitementData)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'id' => $id, 
                        'statut' => $decision,
                        'message' => 'Traitement mis à jour avec succès.'
                    ]);
                    exit;
                }
                $redirect = isset($_GET['from']) && $_GET['from'] === 'admin' ? 'adminIndex' : 'index';
                header('Location: index.php?action=' . $redirect);
                exit;
            }

            if ($isAjax) {
                header('Content-Type: application/json', true, 400);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour.']);
                exit;
            }
            $errors[] = 'Impossible de mettre à jour le traitement.';
        }

        require __DIR__ . '/../views/backoffice/edit_traitement.php';
    }

    public function deleteAction($id) {
        $this->delete($id);
        $redirect = isset($_GET['from']) && $_GET['from'] === 'admin' ? 'adminIndex' : 'index';
        header('Location: index.php?action=' . $redirect);
        exit;
    }

    public function adminIndex() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_demande',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $conges = $this->all($filters);
        $stats = $this->getStats();
        
        $employes = [];
        $stmt = $this->db->query("SELECT id_employe FROM Employe");
        while ($emp = $stmt->fetch()) {
            $employes[] = $this->getEmployeInfo($emp['id_employe']);
        }

        require __DIR__ . '/../views/backoffice/dashboard.php';
    }

    public function calendarAdmin() {
        // Récupérer tous les congés avec le nom de l'employé
        $stmt = $this->db->query("
            SELECT c.*, e.nom, e.prenom 
            FROM Conge c 
            JOIN Employe e ON c.id_employe = e.id_employe
        ");
        $conges = $stmt->fetchAll();

        $events = [];
        $workload = [];

        foreach ($conges as $c) {
            // Déterminer la couleur
            $color = '#f2994a'; // en_attente (orange)
            if ($c['statut'] === 'approuvé') {
                $color = '#2ca95a'; // vert
            } else if ($c['statut'] === 'refusé') {
                $color = '#d9534f'; // rouge
            }

            // FullCalendar exclusive end date
            $end = new DateTime($c['date_fin']);
            $end->modify('+1 day');

            $events[] = [
                'title' => $c['prenom'] . ' ' . $c['nom'] . ' (' . $c['type_conge'] . ')',
                'start' => $c['date_debut'],
                'end' => $end->format('Y-m-d'),
                'color' => $color,
                'textColor' => '#ffffff',
                'allDay' => true
            ];

            // Calcul de la charge (uniquement pour les congés non refusés)
            if ($c['statut'] !== 'refusé') {
                $dStart = new DateTime($c['date_debut']);
                $dEnd = new DateTime($c['date_fin']);
                for ($d = clone $dStart; $d <= $dEnd; $d->modify('+1 day')) {
                    $dayOfWeek = (int)$d->format('N');
                    if ($dayOfWeek <= 5) { // Ignorer les week-ends
                        $dateStr = $d->format('Y-m-d');
                        if (!isset($workload[$dateStr])) {
                            $workload[$dateStr] = 0;
                        }
                        $workload[$dateStr]++;
                    }
                }
            }
        }

        // Ajouter les événements de charge élevée
        foreach ($workload as $date => $count) {
            if ($count >= 2) {
                $events[] = [
                    'title' => "⚠️ $count Absents (Surcharge)",
                    'start' => $date,
                    'allDay' => true,
                    'color' => '#ffebee', // fond rouge très clair
                    'textColor' => '#c62828', // texte rouge foncé
                    'display' => 'background' // affichage en arrière plan
                ];
            }
        }

        $eventsJson = json_encode($events);

        require __DIR__ . '/../views/backoffice/calendar.php';
    }

    public function exportPdf() {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'sort' => $_GET['sort'] ?? 'date_demande',
            'dir' => $_GET['dir'] ?? 'DESC'
        ];
        $conges = $this->all($filters);

        $data = [];
        foreach ($conges as $c) {
            $data[] = [
                'periode' => $c['date_debut'] . ' au ' . $c['date_fin'],
                'type'    => $c['type_conge'],
                'motif'   => $c['motif'],
                'statut'  => $c['statut']
            ];
        }

        $this->outputProfessionalPdf('Rapport_Conges_' . date('Y-m-d') . '.pdf', $data);
    }

    private function outputProfessionalPdf($fileName, $rows) {
        $pdfBody = "%PDF-1.4\n";
        $objects = [];
        
        // Construction du contenu graphique et texte
        $content = "q\n"; 
        // 1. Bandeau d'en-tête (Couleur GreenSecure)
        $content .= "0.42 0.49 0.38 rg\n"; // RGB pour #6b7d62
        $content .= "0 780 595 62 re f\n";
        $content .= "Q\n";
        
        // 2. Texte de l'en-tête
        $content .= "BT\n/F1 18 Tf\n1 1 1 rg\n50 815 Td\n(GreenSecure - Rapport de Gestion des Conges) Tj\nET\n";
        $content .= "BT\n/F1 10 Tf\n1 1 1 rg\n50 795 Td\n(Genere le : " . date('d/m/Y H:i') . ") Tj\nET\n";
        
        // 3. Titres du tableau
        $y = 750;
        $content .= "BT\n/F1 12 Tf\n0 0 0 rg\n50 $y Td\n(PERIODE) Tj\n150 0 Td\n(TYPE) Tj\n150 0 Td\n(MOTIF) Tj\n150 0 Td\n(STATUT) Tj\nET\n";
        
        $content .= "0 0 0 RG\n0.5 w\n50 " . ($y - 5) . " m 550 " . ($y - 5) . " l S\n";
        
        // 4. Données du tableau
        $y -= 25;
        foreach ($rows as $row) {
            if ($y < 50) break; // Simple gestion de fin de page
            
            $content .= "BT\n/F1 10 Tf\n50 $y Td\n(" . $this->pdfSafe($row['periode']) . ") Tj\n";
            $content .= "150 0 Td\n(" . $this->pdfSafe($row['type']) . ") Tj\n";
            $content .= "150 0 Td\n(" . $this->pdfSafe($row['motif']) . ") Tj\n";
            $content .= "150 0 Td\n(" . $this->pdfSafe($row['statut']) . ") Tj\nET\n";
            
            $y -= 20;
        }
        
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj\n";
        $objects[] = "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";
        $objects[] = "5 0 obj << /Length " . strlen($content) . " >> stream\n" . $content . "\nendstream endobj\n";
        
        $pdfContent = $pdfBody;
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdfContent);
            $pdfContent .= $obj;
        }
        
        $xrefPos = strlen($pdfContent);
        $pdfContent .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdfContent .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdfContent .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdfContent .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdfContent .= "startxref\n$xrefPos\n%%EOF";
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    private function pdfSafe($str) {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $str);
    }

}
?>