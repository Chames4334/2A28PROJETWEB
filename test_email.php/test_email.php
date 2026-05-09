<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'hammamioumaima2004@gmail.com';
    $mail->Password = 'jlnp kebc uafx mfle';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('hammamioumaima2004@gmail.com', 'AS Assurance');
    $mail->addAddress('hammamioumaima2004@gmail.com');
    
    $mail->isHTML(true);
    $mail->Subject = 'Test AS Assurance';
    $mail->Body = '<h2>Test réussi !</h2><p>Votre système de notification email fonctionne correctement.</p>';
    
    $mail->send();
    echo "✅ Email envoyé avec succès !";
    
} catch(Exception $e) {
    echo "❌ Erreur : " . $mail->ErrorInfo;
}
?>