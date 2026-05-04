<?php
require_once 'Model/Config/Twilio.php';

$telephone = '+21693932621'; // Ton numéro
$message = "Test SMS depuis AS Assurance - " . date('H:i:s');

// API Twilio via cURL
$url = 'https://api.twilio.com/2010-04-01/Accounts/' . TwilioConfig::$accountSid . '/Messages.json';

$postData = [
    'To' => $telephone,
    'From' => TwilioConfig::$fromNumber,
    'Body' => $message
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, TwilioConfig::$accountSid . ':' . TwilioConfig::$authToken);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h2>Résultat envoi SMS</h2>";
echo "📞 Numéro : $telephone<br>";
echo "📡 Code HTTP : $httpCode<br>";

if($httpCode == 201) {
    echo "✅ SMS envoyé avec succès !<br>";
    echo "📱 Vérifie ton téléphone.";
} elseif($httpCode == 0) {
    echo "❌ Erreur cURL : $error<br>";
    echo "💡 Vérifie ta connexion internet.";
} else {
    echo "❌ Réponse : $response<br>";
}

// Afficher l'erreur détaillée
$json = json_decode($response, true);
if(isset($json['message'])) {
    echo "<br><strong>Message Twilio :</strong> " . $json['message'];
}
?>