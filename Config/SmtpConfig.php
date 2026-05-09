<?php
class SmtpConfig {
    public static function getConfig() {
        return [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'auth' => true,
            'username' => 'hammamioumaima2004@gmail.com',
            'password' => 'ftbx besj buaw aubo', // Mot de passe d'application
            'secure' => 'tls',
            'from_email' => 'hammamioumaima2004@gmail.com',
            'from_name' => 'GS Assurance'
        ];
    }
}
?>