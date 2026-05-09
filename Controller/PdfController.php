<?php
/**
 * PDF Controller - DOMPDF Integration
 * 
 * Generates PDF for insurance claims
 */

require_once ROOT_PATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfController {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Generate PDF for a claim (demande)
     */
    public function generate($demandeId) {
        // Get demande data
        $query = "SELECT d.*, 
                  r.montant as montant_reponse,
                  r.message_admin,
                  r.statut_voiture,
                  r.created_at as date_reponse,
                  t.nom as type_nom,
                  a.nom as atelier_nom,
                  a.adresse as atelier_adresse
                  FROM demande_constat d 
                  LEFT JOIN reponse_constat r ON d.id = r.demande_id
                  LEFT JOIN type_reponse t ON r.type_reponse_id = t.id
                  LEFT JOIN ateliers a ON r.id_atelier = a.id
                  WHERE d.id = :id
                  ORDER BY r.created_at DESC
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $demandeId);
        $stmt->execute();
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$demande) {
            die("Demande non trouvée");
        }
        
        // Build HTML content
        $html = $this->buildHtml($demande);
        
        // Configure DOMPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Output PDF
        $filename = 'constatation_' . $demandeId . '_' . date('Ymd') . '.pdf';
        $dompdf->stream($filename, [
            'Attachment' => true,
            'compress' => true
        ]);
    }
    
    /**
     * Build HTML template for PDF
     */
    private function buildHtml($d) {
        // Status French translation
        $statuses = [
            'soumis' => 'Soumis - En attente',
            'en_cours' => 'En cours de traitement',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            'clos' => 'Clôturé'
        ];
        
        $statut = $statuses[$d['statut']] ?? $d['statut'];
        
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; }
        .header { background: linear-gradient(135deg, #6FAF4C, #A67C52); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header p { font-size: 14px; opacity: 0.9; }
        .content { padding: 30px; }
        .section { margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: bold; color: #6FAF4C; border-bottom: 2px solid #6FAF4C; padding-bottom: 8px; margin-bottom: 15px; }
        .info-grid { display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 35%; padding: 8px 0; font-weight: bold; color: #666; }
        .info-value { display: table-cell; width: 65%; padding: 8px 0; border-bottom: 1px solid #eee; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .status-soumis { background: #fff3cd; color: #856404; }
        .status-en_cours { background: #cce5ff; color: #004085; }
        .status-accepte { background: #d4edda; color: #155724; }
        .status-refuse { background: #f8d7da; color: #721c24; }
        .status-clos { background: #e2e3e5; color: #383d41; }
        .response-box { background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-top: 15px; }
        .response-box h4 { color: #6FAF4C; margin-bottom: 15px; }
        .footer { background: #f5f5f5; padding: 20px 30px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; }
        .watermark { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 80px; opacity: 0.05; font-weight: bold; color: #999; }
    </style>
</head>
<body>
    <div class="watermark">AS ASSURANCE</div>
    
    <div class="header">
        <h1>📋 CONSTATATION D\'ASSURANCE</h1>
        <p>Gestion des Constatations - AS Assurance</p>
    </div>
    
    <div class="content">
        <div class="section">
            <div class="section-title">Informations du Dossier</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Numéro de dossier</div>
                    <div class="info-value"><strong>#' . htmlspecialchars($d['id']) . '</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Statut</div>
                    <div class="info-value">
                        <span class="status-badge status-' . htmlspecialchars($d['statut']) . '">
                            ' . $statut . '
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date de soumission</div>
                    <div class="info-value">' . date('d/m/Y à H:i', strtotime($d['created_at'])) . '</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Informations du Client</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Nom</div>
                    <div class="info-value">' . htmlspecialchars($d['nom']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Prénom</div>
                    <div class="info-value">' . htmlspecialchars($d['prenom']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">' . htmlspecialchars($d['email']) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Téléphone</div>
                    <div class="info-value">' . htmlspecialchars($d['telephone']) . '</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Détails de l\'Accident</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Lieu de l\'accident</div>
                    <div class="info-value">' . nl2br(htmlspecialchars($d['lieu_accident'])) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date de l\'accident</div>
                    <div class="info-value">' . date('d/m/Y', strtotime($d['date_accident'])) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Description</div>
                    <div class="info-value">' . nl2br(htmlspecialchars($d['description'])) . '</div>
                </div>
            </div>
        </div>';
        
        // Add response section if exists
        if (!empty($d['montant_reponse'])) {
            $html .= '
        <div class="section">
            <div class="section-title">Réponse de l\'Assurance</div>
            <div class="response-box">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Type de réponse</div>
                        <div class="info-value">' . htmlspecialchars($d['type_nom'] ?? 'N/A') . '</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Montant</div>
                        <div class="info-value"><strong>' . number_format($d['montant_reponse'], 3, ',', ' ') . ' TND</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Statut du véhicule</div>
                        <div class="info-value">' . htmlspecialchars($d['statut_voiture'] ?? 'En attente') . '</div>
                    </div>';
            
            if (!empty($d['atelier_nom'])) {
                $html .= '
                    <div class="info-row">
                        <div class="info-label">Atelier</div>
                        <div class="info-value">' . htmlspecialchars($d['atelier_nom']) . '<br><small>' . htmlspecialchars($d['atelier_adresse'] ?? '') . '</small></div>
                    </div>';
            }
            
            if (!empty($d['message_admin'])) {
                $html .= '
                    <div class="info-row">
                        <div class="info-label">Message</div>
                        <div class="info-value">' . nl2br(htmlspecialchars($d['message_admin'])) . '</div>
                    </div>';
            }
            
            $html .= '
                </div>
            </div>
        </div>';
        }
        
        $html .= '
    </div>
    
    <div class="footer">
        <p>Document généré le ' . date('d/m/Y à H:i') . '</p>
        <p>AS Assurance - Gestion des Constatations</p>
        <p>Ce document est généré automatiquement et n\'a pas de valeur juridique</p>
    </div>
</body>
</html>';
        
        return $html;
    }
    
    /**
     * Download button for UI
     */
    public function downloadButton($demandeId) {
        return '<a href="index.php?action=generate_pdf&id=' . $demandeId . '" class="btn btn-primary" target="_blank">
            <i class="fas fa-file-pdf"></i> Télécharger PDF
        </a>';
    }
}