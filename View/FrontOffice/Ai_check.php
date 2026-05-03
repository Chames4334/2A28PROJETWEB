<?php
 
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";
    
    define('GEMINI_API_KEY', 'AIzaSyBC7Esdg_Wbo7POov0dCOfl5Pb2Oj6u14Y');
    
    header('Content-Type: application/json');
    
    $d = json_decode(file_get_contents('php://input'), true);
    if (!$d) {
        echo json_encode([
            'verdict' => 'REFUSÉ',
            'résumé' => 'Données invalides.',
            'checks' => []
        ]); 
        exit; 
    }
    
    $today = date('Y-m-d');
    
    $prompt = <<<PROMPT
    Tu es le système de filtrage automatique de GreenSecure, compagnie d'assurance tunisienne.
    Analyse cette demande de souscription avec rigueur. Tu dois rendre un verdict BINAIRE uniquement.
    
    DONNÉES SOUMISES:
    - Offre choisie        : {$d['Title']}
    - Méthode de paiement  : {$d['Payment_method']}
    - Date souscription    : {$d['date_souscription']}
    - Date expiration      : {$d['date_expiration']}
    - Montant payé (DT)    : {$d['Montant_paye']}
    - Numéro de carte      : {$d['cardNumber']}
    - Code postal          : {$d['PostalCode']}
    - Adresse facturation  : {$d['adresse']}
    - Région               : {$d['region']}
    
    RÈGLES DE VALIDATION (date du jour : {$today}):
    1. Méthode de paiement : doit être Carte / Virement / Cheque / Especes
    2. Date souscription   : doit être >= aujourd'hui ({$today})
    3. Date expiration     : doit être > date souscription, écart max 5 ans
    4. Montant             : entre 100 DT et 10 000 DT
    5. Offre               : ne doit pas être vide
    6. Si méthode = Carte :
    a. Numéro de carte  : 15-16 chiffres, doit passer l'algorithme de Luhn, préfixe Visa(4) ou Mastercard(51-55)
    b. Code postal TN   : 4 chiffres entre 1000 et 9999
    c. Adresse          : réelle, pas de caractères aléatoires, minimum 5 caractères
    d. Région           : Tunis, Ariana, Manouba, Nabeul, Bizerte, Sfax, ou Tataouine
    7. Si méthode ≠ Carte  : ignorer les champs carte
    
    IMPORTANT: Tu dois choisir UNIQUEMENT entre ACCEPTÉ ou REFUSÉ. Pas de EN RÉVISION.
    - ACCEPTÉ : toutes les données sont valides
    - REFUSÉ  : au moins une donnée est invalide ou manquante
    
    Réponds UNIQUEMENT en JSON valide, sans texte avant/après:
    {
    "verdict": "ACCEPTÉ" | "REFUSÉ",
    "score": <0-100>,
    "résumé": "<1 phrase claire>",
    "raison_refus": "<si REFUSÉ: raison principale, sinon null>",
    "checks": [
        {"champ": "<nom>", "statut": "ok"|"fail", "message": "<explication courte>"}
    ]
    }
    PROMPT;

    $url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent?key="."AIzaSyBC7Esdg_Wbo7POov0dCOfl5Pb2Oj6u14Y";
    
    $data = [
        "contents" => [[
            "parts" => [
                ["text" => $prompt]
            ]
        ]]
    ];
    
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    
    $response = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);
    
    if ($curlErr || !$response) {
        echo json_encode(['verdict' => 'REFUSÉ', 'score' => 0,
            'résumé' => 'Service IA indisponible.', 'raison_refus' => 'API injoignable', 'checks' => []]);
        exit;
    }
    
    $res   = json_decode($response, true);
    $text   = $res['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $text   = preg_replace('/```json|```/', '', $text);
    $result = json_decode(trim($text), true);
    
    if (!$result || !isset($result['verdict'])) {
        echo json_encode([
            'verdict' => 'REFUSÉ',
            'score' => 0,
            'résumé' => 'Réponse IA non analysable.', 
            'raison_refus' => 'Erreur parsing', 
            'checks' => []
        ]);
        exit;
    }

    if ($result['verdict'] !== 'ACCEPTÉ') 
        $result['verdict'] = 'REFUSÉ';
    
    echo json_encode($result);