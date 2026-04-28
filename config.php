<?php
// config.php - Connexion BDD
session_start();

// Configuration BDD
define('DB_HOST', 'localhost');
define('DB_NAME', 'insurance_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration URLs
define('BASE_URL', 'http://localhost/green_assurance/');
define('BASE_PATH', dirname(__FILE__) . '/');

// Configuration email (pour envoi de vérification)
define('SMTP_FROM', 'noreply@greenassurance.com');

// Limite tentatives login
define('MAX_LOGIN_ATTEMPTS', 6);
define('LOCKOUT_TIME', 15); // minutes

// Connexion PDO
class config {
    private static $pdo = null;
    
    public static function getConnexion() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch(PDOException $e) {
                die("Erreur: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Fonctions d'auth
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'view/auth/login.php');
        exit;
    }
}

// Fonction d'envoi d'email
function sendMail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SMTP_FROM . "\r\n";
    return mail($to, $subject, $message, $headers);
}