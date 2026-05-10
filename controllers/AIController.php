<?php
require_once __DIR__ . '/../config/database.php';

class AIController {
    private $db;
    private $config;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->config = require __DIR__ . '/../config/ai_config.php';
    }

    /**
     * Analyse une demande de congé spécifique
     */
    public function analyze() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            $this->jsonResponse(['error' => 'Données invalides'], 400);
        }

        $id_employe = $data['id_employe'] ?? 1;
        $context = $this->collectContext($id_employe);
        
        $startDate = strtotime($data['date_debut']);
        $endDate = strtotime($data['date_fin']);
        
        $holidays = $context['constraints']['holidays'] ?? [];
        $peakPeriods = $context['constraints']['restricted_periods'] ?? [];
        $maxAbsents = $context['metadata']['max_capacity_risk'] ?? 2;
        $overloadDates = $context['constraints']['team_overload_dates'] ?? [];
        
        $score = 100;
        $pointsPositifs = [];
        $pointsNegatifs = [];
        $status = 'optimal';
        
        $current = $startDate;
        while ($current <= $endDate) {
            $dateStr = date('Y-m-d', $current);
            $dayOfWeek = date('N', $current);
            
            if ($dayOfWeek < 6 && !isset($holidays[$dateStr])) {
                // Check peak periods
                foreach ($peakPeriods as $peak) {
                    if ($dateStr >= $peak['start'] && $dateStr <= $peak['end']) {
                        $score -= 15;
                        if (!in_array("La période chevauche une zone de forte activité.", $pointsNegatifs)) {
                            $pointsNegatifs[] = "La période chevauche une zone de forte activité.";
                        }
                    }
                }
                
                // Check capacity
                if (in_array($dateStr, $overloadDates)) {
                    $score -= 50;
                    $status = 'conflict';
                    if (!in_array("Risque critique de sous-effectif identifié.", $pointsNegatifs)) {
                        $pointsNegatifs[] = "Risque critique de sous-effectif identifié.";
                    }
                }
            } elseif (isset($holidays[$dateStr])) {
                $score += 5; // Bonus for bridging holidays
                if (!in_array("Optimisation d'un jour férié détectée.", $pointsPositifs)) {
                    $pointsPositifs[] = "Optimisation d'un jour férié détectée.";
                }
            }
            
            $current = strtotime('+1 day', $current);
        }
        
        $score = max(0, min(100, $score));
        
        if ($status !== 'conflict') {
            if ($score >= 75) {
                $status = 'optimal';
            } elseif ($score >= 40) {
                $status = 'risky';
            } else {
                $status = 'conflict';
            }
        }
        
        $message = "La période sélectionnée est optimale.";
        $conseils = "Vous pouvez valider cette demande sereinement.";
        if ($status === 'conflict') {
            $message = "Cette demande met en péril la continuité du service.";
            $conseils = "Il est fortement conseillé de modifier les dates.";
        } elseif ($status === 'risky') {
            $message = "La période présente quelques risques pour l'organisation.";
            $conseils = "Vérifiez avec votre équipe avant de soumettre la demande.";
        }
        
        if (empty($pointsPositifs) && $status === 'optimal') {
            $pointsPositifs[] = "Aucun conflit majeur détecté.";
        }

        $this->jsonResponse([
            'status' => $status,
            'score' => $score,
            'message' => $message,
            'points_positifs' => $pointsPositifs,
            'points_negatifs' => $pointsNegatifs,
            'conseils' => $conseils
        ]);
    }

    /**
     * Analyse tout un mois de calendrier pour le manager
     */
    public function analyzeCalendar() {
        // S'assurer que le mois est toujours sur 2 chiffres (ex: '05' au lieu de '5')
        $month = str_pad($_GET['month'] ?? date('m'), 2, '0', STR_PAD_LEFT);
        $year = $_GET['year'] ?? date('Y');
        
        $context = $this->collectCalendarContext($month, $year);
        $conges = $context['conges'];
        $holidays = $context['holidays'];
        $max_absents = $this->config['max_absents_per_team'] ?? 2;
        
        // Build workload map for the month
        $workload_approved = [];
        $workload_pending = [];
        
        foreach ($conges as $leave) {
            $current = strtotime($leave['date_debut']);
            $last = strtotime($leave['date_fin']);
            $is_approved = ($leave['statut'] === 'approuvé');
            $empName = trim(($leave['prenom'] ?? '') . ' ' . ($leave['nom'] ?? ''));
            if (empty($empName)) $empName = 'Employé(e)';
            
            while ($current <= $last) {
                $dateStr = date('Y-m-d', $current);
                if ($is_approved) {
                    $workload_approved[$dateStr][] = $empName;
                } else {
                    $workload_pending[$dateStr][] = $empName;
                }
                $current = strtotime('+1 day', $current);
            }
        }
        
        $risks = [];
        $critical_days = 0;
        $pending_risks = 0;
        
        // 1. Analyse de la charge journalière
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = sprintf("%04d-%02d-%02d", $year, $month, $d);
            
            $approved_emps = array_unique($workload_approved[$date] ?? []);
            $pending_emps = array_unique($workload_pending[$date] ?? []);
            
            $approved_count = count($approved_emps);
            $pending_count = count($pending_emps);
            $total_potential = $approved_count + $pending_count;
            
            if ($approved_count >= $max_absents) {
                $critical_days++;
                $names = implode(', ', $approved_emps);
                $risks[] = [
                    'date' => $date,
                    'reason' => "Seuil critique atteint : $approved_count collaborateurs absents ($names)."
                ];
            } elseif ($total_potential >= $max_absents && $pending_count > 0) {
                $pending_risks++;
                $all_emps = array_unique(array_merge($approved_emps, $pending_emps));
                $names = implode(', ', $all_emps);
                $risks[] = [
                    'date' => $date,
                    'reason' => "Sous-effectif potentiel si validation : $total_potential absents ($names)."
                ];
            }
        }
        
        // 2. Détection des ponts (Bridges)
        $bridges_detected = 0;
        foreach ($holidays as $h_date => $label) {
            if (date('m', strtotime($h_date)) === $month) {
                $dayOfWeek = date('N', strtotime($h_date));
                if ($dayOfWeek == 2) { // Mardi
                    $bridge_date = date('Y-m-d', strtotime('-1 day', strtotime($h_date)));
                    $bridges_detected++;
                    $risks[] = [
                        'date' => $bridge_date,
                        'reason' => "Pont possible avant $label : forte demande, risque de désertion du bureau."
                    ];
                } elseif ($dayOfWeek == 4) { // Jeudi
                    $bridge_date = date('Y-m-d', strtotime('+1 day', strtotime($h_date)));
                    $bridges_detected++;
                    $risks[] = [
                        'date' => $bridge_date,
                        'reason' => "Pont possible après $label : forte demande, risque de désertion du bureau."
                    ];
                }
            }
        }
        
        // 3. Synthèse et Recommandations
        $verdict = 'favorable';
        $summary = "Le planning du mois semble équilibré.";
        $recommendations = "Aucune action particulière n'est requise.";
        $score = 100 - ($critical_days * 10) - ($pending_risks * 3) - ($bridges_detected * 5);
        $score = max(0, min(100, $score));
        
        if ($critical_days >= 3) {
            $verdict = 'critique';
            $summary = "Le mois présente une charge critique avec de multiples risques de sous-effectif.";
            $recommendations = "Bloquez toute nouvelle demande sur les périodes identifiées et envisagez des ajustements urgents.";
        } elseif ($critical_days > 0 || $pending_risks > 0 || $bridges_detected > 0) {
            $verdict = 'tendu';
            $summary = "Le mois présente une charge élevée en milieu de période, avec plusieurs demandes qui testent la limite de l'équipe.";
            $recs = ["Restez vigilant lors de l'approbation de nouvelles demandes aux dates signalées."];
            if ($pending_risks > 0) $recs[] = "Évaluez avec prudence les demandes en attente avant de les valider.";
            if ($bridges_detected > 0) $recs[] = "Anticipez les demandes de pont pour garantir la continuité du service.";
            $recommendations = implode(' ', $recs);
        }
        
        // Filtrer les risques uniques par raison ou date pour ne pas surcharger la vue
        $unique_risks = [];
        $seen = [];
        foreach ($risks as $r) {
            $key = $r['date'] . '-' . explode(':', $r['reason'])[0];
            if (!isset($seen[$key])) {
                $unique_risks[] = $r;
                $seen[$key] = true;
            }
        }
        
        $final_risks = array_slice($unique_risks, 0, 8);
        
        $this->jsonResponse([
            'verdict' => $verdict,
            'summary' => $summary,
            'risks' => $final_risks,
            'recommendations' => $recommendations,
            'team_availability_score' => $score
        ]);
    }

    /**
     * Suggestions de meilleures dates
     */
    public function getSmartSuggestions() {
        $id_employe = $_GET['id_employe'] ?? 1;
        $context = $this->collectContext($id_employe);

        $analysis = $context['algorithmic_pre_analysis'] ?? ['recommended_safe_slots' => []];
        $safeSlots = $analysis['recommended_safe_slots'];
        $holidays = $context['constraints']['holidays'] ?? [];
        $employee_busy_dates = $context['employee_profile']['busy_dates'] ?? [];

        $suggestions = [];
        $maxSuggestions = 3;
        $count = 0;
        
        foreach ($safeSlots as $startDate) {
            if ($count >= $maxSuggestions) break;
            
            // Ne pas suggérer un début de congé le week-end
            if (date('N', strtotime($startDate)) >= 6) continue;
            
            // Calculer une période de congé de 5 jours ouvrés
            $current = strtotime($startDate);
            $daysAdded = 0;
            $isPeriodValid = true;
            
            while ($daysAdded < 4) { // on ajoute 4 jours ouvrés en plus du premier jour
                $current = strtotime('+1 day', $current);
                $dayOfWeek = date('N', $current);
                $dateStr = date('Y-m-d', $current);
                
                // Si l'employé a déjà un congé sur un des jours suivants, on annule cette suggestion
                if (isset($employee_busy_dates[$dateStr])) {
                    $isPeriodValid = false;
                    break;
                }
                
                // Si pas un week-end et pas un jour férié, on le compte
                if ($dayOfWeek < 6 && !isset($holidays[$dateStr])) {
                    $daysAdded++;
                }
            }
            
            if (!$isPeriodValid) continue;
            
            $endDate = date('Y-m-d', $current);

            // Générer un label naturel
            $startFormatted = date('d/m/Y', strtotime($startDate));
            $endFormatted = date('d/m/Y', strtotime($endDate));
            
            $score = 100 - ($count * 5); // Score décroissant pour le classement
            
            $suggestions[] = [
                'debut' => $startDate,
                'fin' => $endDate,
                'label' => "Semaine du $startFormatted",
                'score' => $score,
                'reason' => "Période optimale : la charge d'équipe est faible et la continuité du service est assurée.",
                'impact' => "Impact minime sur l'organisation. Aucun risque de sous-effectif détecté."
            ];
            
            $count++;
        }

        $this->jsonResponse(['suggestions' => $suggestions]);
    }

    /**
     * Collecte tout le contexte nécessaire pour l'IA avec analyse métier avancée
     */
    private function collectContext($id_employe) {
        // 1. Jours fériés 2025
        $holidays = [
            '2025-01-01' => 'Jour de l\'An', '2025-04-21' => 'Lundi de Pâques',
            '2025-05-01' => 'Fête du Travail', '2025-05-08' => 'Victoire 1945',
            '2025-05-29' => 'Ascension', '2025-06-09' => 'Lundi de Pentecôte',
            '2025-07-14' => 'Fête Nationale', '2025-08-15' => 'Assomption',
            '2025-11-01' => 'Toussaint', '2025-11-11' => 'Armistice 1918',
            '2025-12-25' => 'Noël'
        ];

        // 2. Analyse de la disponibilité des équipes (Charge réelle)
        $stmt = $this->db->prepare("SELECT date_debut, date_fin, id_employe FROM Conge WHERE date_fin >= CURDATE() AND statut = 'approuvé'");
        $stmt->execute();
        $approved_leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculer la charge par jour pour les 4 prochains mois
        $workload_map = [];
        foreach ($approved_leaves as $leave) {
            $current = strtotime($leave['date_debut']);
            $last = strtotime($leave['date_fin']);
            while ($current <= $last) {
                $date = date('Y-m-d', $current);
                $workload_map[$date] = ($workload_map[$date] ?? 0) + 1;
                $current = strtotime('+1 day', $current);
            }
        }

        // 3. Identification des périodes critiques (Peaks + Surcharges)
        $critical_dates = [];
        foreach ($workload_map as $date => $count) {
            if ($count >= $this->config['max_absents_per_team']) {
                $critical_dates[] = $date;
            }
        }

        // 4. Historique, Solde et Congés futurs de l'employé
        $stmt = $this->db->prepare("SELECT COUNT(*) as nb FROM Conge WHERE id_employe = ? AND statut = 'approuvé'");
        $stmt->execute([$id_employe]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt_emp = $this->db->prepare("SELECT date_debut, date_fin FROM Conge WHERE id_employe = ? AND statut IN ('approuvé', 'en_attente') AND date_fin >= CURDATE()");
        $stmt_emp->execute([$id_employe]);
        $employee_leaves = $stmt_emp->fetchAll(PDO::FETCH_ASSOC);

        $employee_busy_dates = [];
        foreach ($employee_leaves as $leave) {
            $current = strtotime($leave['date_debut']);
            $last = strtotime($leave['date_fin']);
            while ($current <= $last) {
                $employee_busy_dates[date('Y-m-d', $current)] = true;
                $current = strtotime('+1 day', $current);
            }
        }

        // 5. Exécution de la pré-analyse algorithmique (Logique métier avancée)
        $analysis = $this->performAlgorithmicPreAnalysis($workload_map, $employee_busy_dates);

        return [
            'metadata' => [
                'today' => date('Y-m-d'),
                'max_capacity_risk' => $this->config['max_absents_per_team'],
                'company_peaks' => $this->config['peak_periods']
            ],
            'algorithmic_pre_analysis' => $analysis,
            'constraints' => [
                'holidays' => $holidays,
                'team_overload_dates' => $critical_dates,
                'restricted_periods' => $this->config['peak_periods']
            ],
            'employee_profile' => [
                'total_approved_leaves' => $stats['nb'],
                'busy_dates' => $employee_busy_dates
            ]
        ];
    }

    /**
     * Logique métier avancée : Pré-analyse algorithmique de la charge
     * Identifie les périodes critiques et les risques de sous-effectif sans IA
     */
    private function performAlgorithmicPreAnalysis($workload_map, $employee_busy_dates = []) {
        $critical_zones = [];
        $safe_zones = [];
        $max_allowed = $this->config['max_absents_per_team'];
        
        // On analyse les 90 prochains jours
        for ($i = 0; $i < 90; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            $count = $workload_map[$date] ?? 0;
            
            if ($count >= $max_allowed) {
                $critical_zones[] = [
                    'date' => $date,
                    'risk_level' => 'high',
                    'reason' => 'Seuil d\'absence critique atteint (' . $count . ' absents)'
                ];
            } elseif ($count < $max_allowed && !$this->isPeakPeriod($date) && !isset($employee_busy_dates[$date])) {
                $safe_zones[] = $date;
            }
        }

        return [
            'status' => count($critical_zones) > 10 ? 'alert' : 'stable',
            'health_score' => 100 - (count($critical_zones) * 2),
            'detected_critical_zones' => array_slice($critical_zones, 0, 5),
            'recommended_safe_slots' => $safe_zones
        ];
    }

    /**
     * Vérifie si une date tombe pendant une période de pointe de l'entreprise
     */
    private function isPeakPeriod($date) {
        if (empty($this->config['peak_periods'])) return false;
        
        $md = date('m-d', strtotime($date));
        foreach ($this->config['peak_periods'] as $peak) {
            if ($md >= $peak['start'] && $md <= $peak['end']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Collecte le contexte spécifique pour un mois de calendrier
     */
    private function collectCalendarContext($month, $year) {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $stmt = $this->db->prepare("
            SELECT c.*, e.nom, e.prenom 
            FROM Conge c 
            JOIN Employe e ON c.id_employe = e.id_employe
            WHERE (c.date_debut <= :end AND c.date_fin >= :start)
            AND c.statut != 'refusé'
        ");
        $stmt->execute(['start' => $startDate, 'end' => $endDate]);
        $conges = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $holidays = $this->getHolidaysForYear($year);
        
        return [
            'month' => $month,
            'year' => $year,
            'conges' => $conges,
            'holidays' => $holidays,
            'total_employees' => 3 // Dynamiser si possible
        ];
    }

    private function getHolidaysForYear($year) {
        // Jours fériés fixes
        $holidays = [
            "{$year}-01-01" => "Jour de l'An",
            "{$year}-05-01" => "Fête du Travail",
            "{$year}-05-08" => "Victoire 1945",
            "{$year}-07-14" => "Fête Nationale",
            "{$year}-08-15" => "Assomption",
            "{$year}-11-01" => "Toussaint",
            "{$year}-11-11" => "Armistice 1918",
            "{$year}-12-25" => "Noël"
        ];
        
        // Jours fériés mobiles (basés sur Pâques)
        $easterTimestamp = easter_date($year);
        $easterMonday = date("Y-m-d", strtotime("+1 day", $easterTimestamp));
        $ascension = date("Y-m-d", strtotime("+39 days", $easterTimestamp));
        $pentecostMonday = date("Y-m-d", strtotime("+50 days", $easterTimestamp));
        
        $holidays[$easterMonday] = "Lundi de Pâques";
        $holidays[$ascension] = "Ascension";
        $holidays[$pentecostMonday] = "Lundi de Pentecôte";
        
        ksort($holidays);
        return $holidays;
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
