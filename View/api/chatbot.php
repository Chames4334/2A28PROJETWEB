<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'config_groq.php';

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');

if (empty($userMessage)) {
    echo json_encode(['success' => false, 'error' => 'Message vide'], JSON_UNESCAPED_UNICODE);
    exit;
}

$botResponse = callGroqAPI($userMessage);

echo json_encode([
    'success' => true,
    'response' => $botResponse
], JSON_UNESCAPED_UNICODE);
?>