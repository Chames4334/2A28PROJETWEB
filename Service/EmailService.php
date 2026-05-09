<?php
/**
 * Service d'envoi d'emails
 * Utilise la fonction mail() native de PHP
 * Fonctionne sans Composer ni configuration SMTP
 */

class EmailService {

    /**
     * Envoi un email de confirmation après dépôt de constat
     * 
     * @param string $destinataire Adresse email du client
     * @param string $nom Nom du client
     * @param string $prenom Prénom du client
     * @param int $id_constat Numéro du constat
     * @return bool True si l'email a été envoyé, False sinon
     */
    public static function envoyerConfirmation($destinataire, $nom, $prenom, $id_constat) {
        
        // Vérifier que l'email est valide
        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide : $destinataire");
            return false;
        }
        
        // Sujet de l'email
        $sujet = "Confirmation de votre constat - GS Assurance";
        
        // Message au format HTML
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #F2F2F2; border-radius: 10px; }
                .header { background: #6FAF4C; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: white; padding: 20px; border-radius: 0 0 10px 10px; }
                h2 { color: #A67C52; }
                .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>GS Assurance</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour $nom $prenom,</h2>
                    <p>Nous avons bien reçu votre demande de constat n°<strong>$id_constat</strong>.</p>
                    <p>Un gestionnaire vous répondra dans les meilleurs délais.</p>
                    <p>Vous pouvez suivre l'avancement de votre dossier sur votre espace client.</p>
                    <br>
                    <p>Cordialement,</p>
                    <p><strong>L'équipe GS Assurance</strong></p>
                </div>
                <div class='footer'>
                    <p>Cet email est un message automatique, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Version texte simple (pour les clients qui ne supportent pas le HTML)
        $message_texte = "Bonjour $nom $prenom,\n\n";
        $message_texte .= "Nous avons bien reçu votre demande de constat n°$id_constat.\n";
        $message_texte .= "Un gestionnaire vous répondra dans les meilleurs délais.\n\n";
        $message_texte .= "Cordialement,\nL'équipe GS Assurance";
        
        // En-têtes pour l'email HTML
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: GS Assurance <noreply@gsassurance.tn>\r\n";
        $headers .= "Reply-To: contact@gsassurance.tn\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Envoi de l'email
        return mail($destinataire, $sujet, $message, $headers);
    }
    
    /**
     * Envoi un email de réponse après traitement du constat
     * 
     * @param string $destinataire Adresse email du client
     * @param string $nom Nom du client
     * @param string $prenom Prénom du client
     * @param int $id_constat Numéro du constat
     * @param string $type_reponse Type de réponse (remboursement/atelier)
     * @param array $details Détails (montant ou nom atelier)
     * @return bool True si l'email a été envoyé, False sinon
     */
    public static function envoyerReponse($destinataire, $nom, $prenom, $id_constat, $type_reponse, $details) {
        
        // Vérifier que l'email est valide
        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            error_log("Email invalide : $destinataire");
            return false;
        }
        
        // Sujet de l'email
        $sujet = "Réponse à votre constat - GS Assurance";
        
        // Construction du message selon le type de réponse
        if ($type_reponse === 'remboursement') {
            $montant = isset($details['montant']) ? $details['montant'] : 'à déterminer';
            $message = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #F2F2F2; border-radius: 10px; }
                    .header { background: #6FAF4C; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: white; padding: 20px; border-radius: 0 0 10px 10px; }
                    .montant { font-size: 24px; color: #6FAF4C; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>GS Assurance</h1>
                    </div>
                    <div class='content'>
                        <h2>Bonjour $nom $prenom,</h2>
                        <p>Votre constat n°<strong>$id_constat</strong> a été traité.</p>
                        <p><strong>Décision :</strong> Remboursement</p>
                        <p><strong>Montant :</strong> <span class='montant'>$montant €</span></p>
                        <p>Le virement sera effectué sur votre compte bancaire sous 48h.</p>
                        <br>
                        <p>Cordialement,</p>
                        <p><strong>L'équipe GS Assurance</strong></p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $message_texte = "Bonjour $nom $prenom,\n\n";
            $message_texte .= "Votre constat n°$id_constat a été traité.\n";
            $message_texte .= "Décision : Remboursement de $montant €\n";
            $message_texte .= "Le virement sera effectué sous 48h.\n\n";
            $message_texte .= "Cordialement,\nL'équipe GS Assurance";
        } else {
            $atelier_nom = isset($details['atelier_nom']) ? $details['atelier_nom'] : 'un atelier partenaire';
            $message = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #F2F2F2; border-radius: 10px; }
                    .header { background: #6FAF4C; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: white; padding: 20px; border-radius: 0 0 10px 10px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>GS Assurance</h1>
                    </div>
                    <div class='content'>
                        <h2>Bonjour $nom $prenom,</h2>
                        <p>Votre constat n°<strong>$id_constat</strong> a été traité.</p>
                        <p><strong>Décision :</strong> Réparation en atelier partenaire</p>
                        <p><strong>Atelier :</strong> $atelier_nom</p>
                        <p>Zéro avance de frais, votre véhicule sera pris en charge rapidement.</p>
                        <br>
                        <p>Cordialement,</p>
                        <p><strong>L'équipe GS Assurance</strong></p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $message_texte = "Bonjour $nom $prenom,\n\n";
            $message_texte .= "Votre constat n°$id_constat a été traité.\n";
            $message_texte .= "Décision : Réparation à l'atelier $atelier_nom\n";
            $message_texte .= "Zéro avance de frais.\n\n";
            $message_texte .= "Cordialement,\nL'équipe GS Assurance";
        }
        
        // En-têtes pour l'email HTML
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: GS Assurance <noreply@gsassurance.tn>\r\n";
        $headers .= "Reply-To: contact@gsassurance.tn\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Envoi de l'email
        return mail($destinataire, $sujet, $message, $headers);
    }
    
    /**
     * Envoi un email simple (version simplifiée)
     * 
     * @param string $destinataire Adresse email du client
     * @param string $sujet Sujet de l'email
     * @param string $message Message (peut contenir du HTML)
     * @return bool
     */
    public static function envoyer($destinataire, $sujet, $message) {
        if (!filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: GS Assurance <noreply@gsassurance.tn>\r\n";
        
        return mail($destinataire, $sujet, $message, $headers);
    }
}
?>