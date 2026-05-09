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

// Configuration email (GMAIL SETTINGS)
define('SMTP_FROM', 'hasnichames70@gmail.com');
define('SMTP_FROM_NAME', 'Green Assurance');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'hasnichames70@gmail.com');
define('SMTP_PASS', 'txzh lwod mpnc vmbb');  // ← Remplace par ton mot de passe

// Limite tentatives login
define('MAX_LOGIN_ATTEMPTS', 6);
define('LOCKOUT_TIME', 15);

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

// Fonction d'envoi d'email avec PHPMailer (VRAI EMAIL)
function sendMail($to, $subject, $message) {
    require_once BASE_PATH . 'src/Exception.php';
    require_once BASE_PATH . 'src/PHPMailer.php';
    require_once BASE_PATH . 'src/SMTP.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}
?>