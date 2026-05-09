<?php
echo "=== TEST TWILIO ===<br><br>";

// Vérifier les chemins
$paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/vendor/src/Twilio/Rest/Client.php',
    __DIR__ . '/vendor/src/Twilio/Rest/Client.php',
];

foreach($paths as $path) {
    echo "Chemin: $path<br>";
    if(file_exists($path)) {
        echo "✅ FICHIER TROUVE<br><br>";
    } else {
        echo "❌ Fichier non trouvé<br><br>";
    }
}

// Essayer d'inclure
if(file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "Autoload inclus<br>";
    
    if(class_exists('Twilio\Rest\Client')) {
        echo "✅ Classe Twilio\\Rest\\Client trouvée !";
    } else {
        echo "❌ Classe Twilio\\Rest\\Client NON trouvée";
    }
} else {
    echo "autoload.php non trouvé";
}
?>