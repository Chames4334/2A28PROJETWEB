<?php
// Verbose SMTP test using PHPMailer to capture SMTP conversation (for debugging auth issues)
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$to = $_GET['to'] ?? null;
if (!$to) {
    echo "Usage: test_smtp_verbose.php?to=you@example.com\n";
    exit;
}

$cfg = [];
if (file_exists(__DIR__ . '/Config/SmtpConfig.php')) {
    require_once __DIR__ . '/Config/SmtpConfig.php';
    if (class_exists('SmtpConfig')) {
        try { $cfg = SmtpConfig::getConfig(); } catch (Exception $e) { $cfg = []; }
    }
}

$smtpHost = $cfg['host'] ?? 'smtp.gmail.com';
$smtpPort = $cfg['port'] ?? 587;
$smtpUser = $cfg['username'] ?? getenv('GMAIL_USERNAME') ?: '';
$smtpPass = $cfg['password'] ?? getenv('GMAIL_APP_PASSWORD') ?: '';
$smtpSecure = $cfg['secure'] ?? 'tls';
$from = $cfg['from_email'] ?? $smtpUser;
$fromName = $cfg['from_name'] ?? 'AS Assurance (test)';

echo "<h2>Verbose SMTP test</h2>";
echo "<p>To: " . htmlspecialchars($to) . "</p>";
echo "<p>Host: " . htmlspecialchars($smtpHost) . ":" . htmlspecialchars($smtpPort) . " (secure=" . htmlspecialchars($smtpSecure) . ")</p>";

if (empty($smtpPass)) {
    echo "<p style='color:orange'><strong>Warning:</strong> No SMTP password configured. Set an app password in Config/SmtpConfig.php or environment variable GMAIL_APP_PASSWORD.</p>";
}

$mail = new PHPMailer(true);
$debugLog = '';
// capture debug output
$mail->SMTPDebug = 2; // client and server messages
$mail->Debugoutput = function($str, $level) use (&$debugLog) { $debugLog .= htmlspecialchars($str) . "<br>\n"; };

try {
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    if (strtolower($smtpSecure) === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    $mail->Port = (int)$smtpPort;

    $mail->setFrom($from, $fromName);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'Test SMTP verbose';
    $mail->Body = 'Test SMTP via PHPMailer (verbose). If you see this, SMTP succeeded.';

    $mail->send();
    echo "<p style='color:green'><strong>SMTP send succeeded.</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:red'><strong>SMTP send failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h3>Debug output</h3>";
echo "<div style='background:#111;color:#eee;padding:12px;border-radius:6px;margin:8px 0;line-height:1.35;font-family:monospace;'>" . $debugLog . "</div>";

echo "<h3>Next steps</h3>";
echo "<ul>";
echo "<li>Ensure account has 2FA and generate an <strong>App Password</strong> (Google Account > Security > App Passwords) then place it in <code>Config/SmtpConfig.php</code> as the 'password' value (or set environment variable <code>GMAIL_APP_PASSWORD</code>).</li>";
echo "<li>Enable <code>extension=openssl</code> in <code>php.ini</code> and restart Apache.</li>";
echo "<li>Run PowerShell: <code>Test-NetConnection -ComputerName smtp.gmail.com -Port 587</code> to verify connectivity.</li>";
echo "</ul>";
