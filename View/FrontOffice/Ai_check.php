<?php
 
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlTypes.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/ControlOffre.php";
    include "C:/xampp/htdocs/GreenSecure/Controller/Controlnscription.php";
    
    define('GEMINI_API_KEY','AIzaSyDhQcd9XHrcSR7xJ4QWC9Jjo7PN--qUVIM');
    
    header('Content-Type: application/json; charset=utf-8');

    ini_set('display_errors', 0);
    error_reporting(0);
    
    $d = json_decode(file_get_contents('php://input'), true);
    if (!$d) {
        echo json_encode([
            'verdict'=>'REFUSÉ',
            'résumé'=>'Données invalides.',
            'checks'=>[],JSON_UNESCAPED_UNICODE
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
    
    Réponds avec UNIQUEMENT cet objet JSON, sans markdown ni texte autour:
    {"verdict":"ACCEPTE ou REFUSE","score":90,"resume":"phrase","raison_refus":"raison ou null","checks":[{"champ":"nom","statut":"ok ou fail","message":"explication"}]}
    PROMPT;

    $payload = json_encode([
        "contents"=>[[
            "parts"=>[["text"=>$prompt]]
        ]],
        "generationConfig"=>[
            "temperature"=>0,
        ]
    ]);
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=".GEMINI_API_KEY;
    
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_POST=> true,
        CURLOPT_POSTFIELDS=>$payload,
        CURLOPT_HTTPHEADER=>[
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
    
    $res=json_decode($response, true);
    if (isset($res['error'])) {
        echo json_encode([
            'verdict'=>'REFUSÉ',
            'score'=>0,
            'résumé'=>'Erreur API Gemini.',
            'raison_refus'=> 'Code '.$res['error']['code'].': '. $res['error']['message'],
            'checks'=>[]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (empty($res['candidates'])) {
        echo json_encode([
            'verdict'=>'REFUSÉ',
            'score'=>0,
            'résumé'=>'Pas de réponse de Gemini.',
            'raison_refus'=>'Réponse brute: '.substr($response, 0, 300),
            'checks'=>[]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $text   = $res['candidates'][0]['content']['parts'][0]['text'] ?? '';

    $text = trim($text);
    $text = preg_replace('/^```json\s*/i', '', $text);
    $text = preg_replace('/^```\s*/i', '', $text);
    $text = preg_replace('/\s*```$/i', '', $text);
    $text = trim($text);

    if (preg_match('/\{.*\}/s', $text, $matches)) {
        $text = $matches[0];
    }
    $result = json_decode($text, true);
    
    if (!$result || !isset($result['verdict'])) {
        echo json_encode([
            'verdict'=>'REFUSÉ',
            'score'=>0,
            'résumé'=>'Réponse IA non analysable.', 
            'raison_refus'=>'Brut reçu: '.substr($text, 0, 150), 
            'checks'=>[]
        ]);
        exit;
    }

    $v = strtoupper(trim($result['verdict']));
    $result['verdict']=($v==='ACCEPTE'||$v==='ACCEPTÉ') ? 'ACCEPTÉ':'REFUSÉ';

    if (!isset($result['résumé']) && isset($result['resume'])) 
        $result['résumé']=$result['resume'];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);