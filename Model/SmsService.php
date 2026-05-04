<?php
/**
 * SMS Service - Twilio Integration
 * 
 * Handles automatic SMS sending for notifications
 */

require_once ROOT_PATH . 'Model/Config/Twilio.php';

class SmsService {
    private $conn;
    private $logFile;
    
    public function __construct($db = null) {
        $this->conn = $db;
        $this->logFile = ROOT_PATH . 'logs/sms.log';
    }
    
    /**
     * Send SMS via Twilio API
     * 
     * @param string $to Recipient phone number (with country code)
     * @param string $message Message content
     * @return array Result with success status and details
     */
    public function sendSms($to, $message) {
        // Validate phone number
        $to = $this->formatPhoneNumber($to);
        
        if (!$to) {
            return [
                'success' => false,
                'error' => 'Numéro de téléphone invalide'
            ];
        }
        
        // Prepare Twilio API request
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . TwilioConfig::$accountSid . '/Messages.json';
        
        $postData = [
            'To' => $to,
            'From' => TwilioConfig::$fromNumber,
            'Body' => $message
        ];
        
        // cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, TwilioConfig::$accountSid . ':' . TwilioConfig::$authToken);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Log the attempt
        $this->logSms($to, $message, $httpCode, $response);
        
        if ($httpCode == 201) {
            return [
                'success' => true,
                'message' => 'SMS envoyé avec succès',
                'twilio_response' => json_decode($response, true)
            ];
        } else {
            $errorData = json_decode($response, true);
            return [
                'success' => false,
                'error' => $errorData['message'] ?? 'Erreur d\'envoi SMS',
                'http_code' => $httpCode
            ];
        }
    }
    
    /**
     * Format phone number to E.164 format
     */
    private function formatPhoneNumber($phone) {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0 (Tunisia), replace with +216
        if (substr($phone, 0, 1) === '0') {
            $phone = '+216' . substr($phone, 1);
        }
        
        // If doesn't start with +, add +216
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+216' . $phone;
        }
        
        // Validate length (Tunisia numbers are typically 8 digits after +216)
        if (strlen($phone) < 11 || strlen($phone) > 13) {
            return false;
        }
        
        return $phone;
    }
    
    /**
     * Log SMS to file
     */
    private function logSms($to, $message, $httpCode, $response) {
        $timestamp = date('Y-m-d H:i:s');
        $status = ($httpCode == 201) ? 'SUCCESS' : 'FAILED';
        
        $logEntry = "$timestamp - $status - SMS à $to : $message\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Send notification for claim status change
     */
    public function notifierStatut($demandeId, $telephone, $nom, $statut) {
        $messages = [
            'soumis' => "Bonjour $nom, votre déclaration #$demandeId a été reçue et est en attente de traitement. - AS Assurance",
            'en_cours' => "Bonjour $nom, votre dossier #$demandeId est en cours de traitement. Nous vous tiendrons informé. - AS Assurance",
            'accepte' => "Bonjour $nom, bonne nouvelle! Votre dossier #$demandeId a été accepté. Consultez votre email pour les détails. - AS Assurance",
            'refuse' => "Bonjour $nom, nous regrettons de vous informer que votre dossier #$demandeId a été refusé. Contactez-nous pour plus d'informations. - AS Assurance",
            'clos' => "Bonjour $nom, votre dossier #$demandeId est clôturé. Merci de votre confiance. - AS Assurance"
        ];
        
        $message = $messages[$statut] ?? "Bonjour $nom, mise à jour de votre dossier #$demandeId. Statut: $statut - AS Assurance";
        
        return $this->sendSms($telephone, $message);
    }
    
    /**
     * Send response notification
     */
    public function notifierReponse($demandeId, $telephone, $nom, $typeReponse, $montant) {
        $message = "Bonjour $nom, nous avons traité votre dossier #$demandeId. 
Type: $typeReponse
Montant: " . number_format($montant, 3, ',', ' ') . " TND
Consultez votre espace pour les détails. - AS Assurance";
        
        return $this->sendSms($telephone, $message);
    }
    
    /**
     * Get SMS log
     */
    public function getLogs($limit = 50) {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$limit);
    }
}