<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class ChatbotController {
    public function index() {
        require_once __DIR__ . '/../View/chatbot/index.php';
    }

    // Handle AJAX chat message POST and return JSON
    public function chat() {
        $message = trim($_POST['message'] ?? '');
        $conversation_id = $_POST['conversation_id'] ?? null;

        if (!$message) {
            echo json_encode(['success' => false, 'error' => 'Message vide']);
            return;
        }

        if (!$conversation_id) {
            $conversation_id = uniqid('conv_', true);
        }

        if (!isset($_SESSION['chat_conversations'])) {
            $_SESSION['chat_conversations'] = [];
        }

        if (!isset($_SESSION['chat_conversations'][$conversation_id])) {
            $_SESSION['chat_conversations'][$conversation_id] = [];
        }

        // Save user message
        $_SESSION['chat_conversations'][$conversation_id][] = ['role' => 'user', 'text' => $message, 'ts' => time()];

        // Generate assistant response (simple rule-based responder)
        $response = $this->simpleResponder($message);

        // Save assistant response
        $_SESSION['chat_conversations'][$conversation_id][] = ['role' => 'assistant', 'text' => $response, 'ts' => time()];

        echo json_encode([
            'success' => true,
            'conversation_id' => $conversation_id,
            'response' => $response
        ]);
    }

    public function clear() {
        $conversation_id = $_POST['conversation_id'] ?? null;
        if ($conversation_id && isset($_SESSION['chat_conversations'][$conversation_id])) {
            unset($_SESSION['chat_conversations'][$conversation_id]);
        }
        echo json_encode(['success' => true]);
    }

    private function simpleResponder($message) {
        $m = mb_strtolower(trim($message), 'UTF-8');

        // Greetings
        if (preg_match('/\b(bonjour|salut|hello|hi)\b/u', $m)) {
            return "Bonjour 👋 Je suis l'assistant d'AS Assurance. Vous pouvez par exemple :\n- 'déclarer' pour une nouvelle déclaration\n- 'suivre' + email ou numéro pour suivre un dossier\n- 'contact' pour les coordonnées du support";
        }

        // Declaration intent
        if (preg_match('/\b(déclar|declar|déclaration|declaration|sinistr)\b/u', $m)) {
            return "Pour déclarer un sinistre, allez sur : /gs_assurance/front/index.php?action=declaration\nSouhaitez‑vous que je vous guide étape par étape ?";
        }

        // Follow / status intent with email or number extraction
        if (preg_match('/\b(suiv|statut|suivre|avancement)\b/u', $m)) {
            // look for an email
            if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $message, $em)) {
                return "Je peux chercher le dossier pour l'email : " . $em[0] . ". (Fonctionnalité de recherche non activée ici)";
            }
            // look for a numeric reference
            if (preg_match('/\b(\d{3,})\b/', $m, $num)) {
                return "Recherche du dossier numéro " . $num[1] . " ... (fonctionnalité non activée ici).";
            }
            return "Donnez l'email ou le numéro de dossier pour que je recherche : par exemple 'suivre mon dossier email@exemple.tn'";
        }

        // Contact intent
        if (preg_match('/\b(contact|assistance|support|aide)\b/u', $m)) {
            return "Contact service client : +216 70 123 456, email : contact@asassurance.tn. Voulez‑vous que je vous affiche la page de contact ?";
        }

        // Atelier / réparation
        if (preg_match('/\b(atelier|répar|repar|réparation|reparation)\b/u', $m)) {
            return "Pour choisir un atelier, sélectionnez 'Atelier' dans le formulaire de déclaration et choisissez votre gouvernorat. Page : /gs_assurance/front/index.php?action=declaration";
        }

        // Remboursement
        if (preg_match('/\b(rembours|remboursement|indemn)\b/u', $m)) {
            return "Pour une demande de remboursement, choisissez 'Remboursement' dans la déclaration et indiquez le montant estimé. Souhaitez‑vous un lien vers la déclaration ?";
        }

        // If user provided an email anywhere
        if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $message, $em)) {
            return "J'ai trouvé l'email " . $em[0] . ". Pour rechercher un dossier, écrivez : 'suivre ' + votre email.";
        }

        // Help suggestions fallback
        return "Désolé, je n'ai pas compris. Essayez : 'déclarer', 'suivre mon dossier email@exemple.tn', 'contact', 'atelier' ou 'remboursement'.";
    }
}
