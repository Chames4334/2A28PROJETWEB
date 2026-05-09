<?php
define('GROQ_API_KEY', '');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

function callGroqAPI($userMessage) {
    $data = [
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            [
                'role' => 'system',
                'content' => "Tu es GreenBot, l'assistant virtuel de Green Assurance, une compagnie d'assurance éco-responsable. Réponds toujours en français, de manière polie, concise et professionnelle. Ne réponds qu'aux questions liées à l'assurance, à l'environnement ou aux services de Green Assurance."
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 1024,
    ];

    $ch = curl_init(GROQ_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return "❌ Erreur réseau : " . $curlError;
    }

    $decoded = json_decode($response, true);

    if ($httpCode === 200) {
        return $decoded['choices'][0]['message']['content']
            ?? "Désolé, je n'ai pas pu générer de réponse.";
    }

    $errorDetail = $decoded['error']['message'] ?? $response;
    return "⚠️ Erreur API (Code $httpCode) : $errorDetail";
}
?>