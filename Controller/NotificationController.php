<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
// Optional SMTP config helper
if (file_exists(__DIR__ . '/../Config/SmtpConfig.php')) {
    require_once __DIR__ . '/../Config/SmtpConfig.php';
}

class NotificationController {
    private $conn;
    private $fromEmail = 'hammamioumaima@gmail.com';
    private $fromName = 'AS Assurance';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Send email with improved HTML template
     */
    public function envoyerEmail($destinataire, $sujet, $message, $template = 'default') {
        $mail = new PHPMailer(true);
        
        try {
            // Try SMTP if configuration provided, otherwise fallback to PHP mail()
            $body = $this->getEmailTemplate($sujet, $message, $template);

            // Load SMTP config: prefer Config/SmtpConfig.php if present, otherwise env var
            $smtpConfig = [];
            if (class_exists('SmtpConfig')) {
                try { $smtpConfig = SmtpConfig::getConfig(); } catch (Exception $e) { $smtpConfig = []; }
            }

            $smtpHost = $smtpConfig['host'] ?? 'smtp.gmail.com';
            $smtpPort = $smtpConfig['port'] ?? 587;
            $smtpAuth = isset($smtpConfig['auth']) ? (bool)$smtpConfig['auth'] : true;
            $smtpUser = $smtpConfig['username'] ?? getenv('GMAIL_USERNAME') ?: $this->fromEmail;
            $smtpPass = $smtpConfig['password'] ?? getenv('GMAIL_APP_PASSWORD') ?: '';
            $smtpSecure = $smtpConfig['secure'] ?? 'tls';
            $smtpFromEmail = $smtpConfig['from_email'] ?? $this->fromEmail;
            $smtpFromName = $smtpConfig['from_name'] ?? $this->fromName;

            $smtpError = '';
            if (!empty($smtpPass) && $smtpAuth) {
                try {
                    $mail->isSMTP();
                    $mail->Host = $smtpHost;
                    $mail->SMTPAuth = true;
                    $mail->Username = $smtpUser;
                    $mail->Password = $smtpPass;
                    // set encryption constant
                    if (strtolower($smtpSecure) === 'ssl') {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    } else {
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    }
                    $mail->Port = (int)$smtpPort;

                    // Expéditeur et destinataire
                    $mail->setFrom($smtpFromEmail, $smtpFromName);
                    $mail->addAddress($destinataire);

                    // Contenu
                    $mail->isHTML(true);
                    $mail->Subject = $sujet;
                    $mail->Body    = $body;
                    $mail->AltBody = strip_tags($message);

                    $mail->send();
                    return [ 'success' => true, 'message' => 'Email envoyé avec succès (SMTP)' ];
                } catch(Exception $e) {
                    $smtpError = $e->getMessage() . ' | PHPMailer: ' . ($mail->ErrorInfo ?? 'no info');
                    error_log("SMTP Email Error: " . $smtpError);
                    // fall through to mail() fallback
                }
            }

            // Fallback: use PHP mail() (useful for local dev when SMTP not configured)
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $this->fromName . " <" . $this->fromEmail . ">\r\n";

            // Attempt PHP mail() as fallback and capture last error
            $sent = @mail($destinataire, $sujet, $body, $headers);
            if ($sent) {
                return [ 'success' => true, 'message' => 'Email envoyé avec succès (mail())' ];
            } else {
                $last = error_get_last();
                $mailErr = $last['message'] ?? 'unknown';
                $fullErr = 'SMTP error: ' . ($smtpError ?: 'no smtp error captured') . ' | mail() error: ' . $mailErr;
                error_log("PHP mail() failed to send to $destinataire | " . $fullErr);
                return [ 'success' => false, 'error' => 'Impossible d\'envoyer l\'email (SMTP et mail() ont échoué): ' . $fullErr ];
            }
        }
        catch (Exception $e) {
            error_log("Email Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $mail->ErrorInfo ?? $e->getMessage()
            ];
        }
    }
    
    /**
     * Get HTML email template
     */
    private function getEmailTemplate($sujet, $message, $template = 'default') {
        $styles = '
            <style>
                body { margin: 0; padding: 0; font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #6FAF4C, #A67C52); padding: 30px; text-align: center; color: white; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; background: #ffffff; }
                .message-box { background: #f8f9fa; border-left: 4px solid #6FAF4C; padding: 20px; margin: 20px 0; }
                .footer { background: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .btn { display: inline-block; padding: 12px 24px; background: #6FAF4C; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
                .status { display: inline-block; padding: 5px 15px; border-radius: 3px; font-size: 12px; font-weight: bold; }
                .status-soumis { background: #fff3cd; color: #856404; }
                .status-en_cours { background: #cce5ff; color: #004085; }
                .status-accepte { background: #d4edda; color: #155724; }
                .status-refuse { background: #f8d7da; color: #721c24; }
            </style>
        ';
        
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    ' . $styles . '
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 AS ASSURANCE</h1>
            <p>Gestion des Constatations d\'Assurance</p>
        </div>
        <div class="content">
            <h2 style="color: #6FAF4C;">' . htmlspecialchars($sujet) . '</h2>
            <div class="message-box">
                ' . nl2br(htmlspecialchars($message)) . '
            </div>
            <p style="color: #666; font-size: 12px;">
                Si vous avez des questions, contactez-nous au +216 93 932 621
            </p>
        </div>
        <div class="footer">
            <p>© ' . date('Y') . ' AS Assurance - Tous droits réservés</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Send confirmation email when user submits a claim
     */
    public function notifierConfirmationDemande($demandeId, $email, $nom, $prenom) {
        $sujet = 'Confirmation de votre déclaration - Dossier #' . $demandeId;
        $message = "Bonjour $prenom $nom,

Nous avons bien reçu votre déclaration de sinistre.

Numéro de dossier: $demandeId
Statut: En attente de traitement

Notre équipe va examiner votre dossier et vous contactera dans les plus brefs délais.

Vous pouvez suivre l'avancement de votre dossier sur notre site:
" . (isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : '') . "/front/pages/historique.php

Cordialement,
L'équipe AS Assurance";

        $res = $this->envoyerEmail($email, $sujet, $message, 'confirmation');
        if (isset($_SESSION)) {
            if ($res['success']) {
                $_SESSION['notif_msg'] = "✅ Email de confirmation envoyé à $email";
            } else {
                $_SESSION['notif_msg'] = "❌ Erreur Email: " . ($res['error'] ?? 'Erreur inconnue');
            }
        }
        return $res;
    }

    /**
     * Send email when claim status changes
     */
    public function notifierStatutDemande($demandeId, $email, $nom, $prenom, $statut) {
        $statusLabels = [
            'soumis' => 'Soumis - En attente',
            'en_cours' => 'En cours de traitement',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            'clos' => 'Clôturé'
        ];
        
        $statutLabel = $statusLabels[$statut] ?? $statut;
        
        $sujet = 'Mise à jour de votre dossier #' . $demandeId . ' - AS Assurance';
        $message = "Bonjour $prenom $nom,

Votre dossier a été mis à jour.

Numéro de dossier: $demandeId
Nouveau statut: $statutLabel

";
        
        if ($statut === 'en_cours') {
            $message .= "Votre déclaration est actuellement en cours de traitement par nos services.";
        } elseif ($statut === 'accepte') {
            $message .= "Bonne nouvelle! Votre demande a été acceptée. Consultez les détails dans votre espace client.";
        } elseif ($statut === 'refuse') {
            $message .= "Nous regrettons de vous informer que votre demande a été refusée. Pour plus d'informations, veuillez nous contacter.";
        } elseif ($statut === 'clos') {
            $message .= "Votre dossier est désormais clôturé. Nous vous remercions de votre confiance.";
        }
        
        $message .= "

Cordialement,
L'équipe AS Assurance";

        $res = $this->envoyerEmail($email, $sujet, $message, 'statut');
        if (isset($_SESSION)) {
            if ($res['success']) {
                $_SESSION['notif_msg'] = "✅ Email de mise à jour envoyé à $email";
            } else {
                $_SESSION['notif_msg'] = "❌ Erreur Email: " . ($res['error'] ?? 'Erreur inconnue');
            }
        }
        return $res;
    }

    /**
     * Send email when response is added to claim
     */
    public function notifierReponse($demandeId, $email, $nom, $prenom, $typeReponse, $montant) {
        $sujet = 'Réponse à votre dossier #' . $demandeId . ' - AS Assurance';
        $message = "Bonjour $prenom $nom,

Nous avons traité votre dossier.

Numéro de dossier: $demandeId
Type de réponse: $typeReponse
Montant: " . number_format($montant, 3, ',', ' ') . " TND

Connectez-vous à votre espace client pour voir tous les détails de la réponse.

Cordialement,
L'équipe AS Assurance";

        $res = $this->envoyerEmail($email, $sujet, $message, 'reponse');
        if (isset($_SESSION)) {
            if ($res['success']) {
                $_SESSION['notif_msg'] = "✅ Email de réponse envoyé à $email";
            } else {
                $_SESSION['notif_msg'] = "❌ Erreur Email: " . ($res['error'] ?? 'Erreur inconnue');
            }
        }
        return $res;
    }

    public function envoyerContactParTel($telephone, $emailDest, $type) {
        // ── Twilio credentials ─────────────────────────────────────────────────
        $twilioSid   = ''; // <-- your Account SID
        $twilioToken = '';              // <-- your Auth Token
        $twilioFrom  = '+19785414678'; // <-- This is your official Twilio Phone Number!
        // ──────────────────────────────────────────────────────────────────────

        $messageText = "Bonjour, ceci est une notification de AS Assurance. Votre dossier a été mis à jour.";
        $errors = [];

        // ── Send SMS via Twilio ───────────────────────────────────────────────
        if ($type === 'sms' || $type === 'lesdeux') {
            // Normalise Tunisian number to E.164
            $tel = preg_replace('/\s+/', '', $telephone);
            if (preg_match('/^[0-9]{8}$/', $tel)) {
                $tel = '+216' . $tel;
            } elseif (preg_match('/^00216/', $tel)) {
                $tel = '+' . ltrim($tel, '0');
            } elseif (!preg_match('/^\+/', $tel)) {
                $tel = '+' . $tel;
            }

            try {
                require_once __DIR__ . '/../vendor/autoload.php';
                $client = new Twilio\Rest\Client($twilioSid, $twilioToken);
                $client->messages->create($tel, [
                    'from' => $twilioFrom,
                    'body' => $messageText,
                ]);
                $_SESSION['notif_msg'] = "✅ SMS envoyé avec succès à $tel";
            } catch (\Exception $e) {
                error_log('Twilio SMS Error: ' . $e->getMessage());
                $errors[] = 'SMS: ' . $e->getMessage();
                $_SESSION['notif_msg'] = "❌ Erreur SMS: " . $e->getMessage();
            }
        }

        // ── Send Email ────────────────────────────────────────────────────────
        if ($type === 'email' || $type === 'lesdeux') {
            $destinataire = !empty($emailDest) ? $emailDest : $this->fromEmail;
            $result = $this->envoyerEmail(
                $destinataire,
                'Notification AS Assurance',
                $messageText
            );
            if ($result['success']) {
                $_SESSION['notif_msg'] = "✅ Email envoyé avec succès";
            } else {
                $errors[] = 'Email: ' . ($result['error'] ?? 'erreur inconnue');
                $_SESSION['notif_msg'] = "❌ Erreur Email: " . ($result['error'] ?? 'erreur inconnue');
            }
        }

        if (!empty($errors) && $type === 'lesdeux') {
            $_SESSION['notif_msg'] = "⚠️ Erreurs: " . implode(' | ', $errors);
        }
    }
}