<?php
// controller/ControlAI.php
include_once __DIR__ . '/../config.php';

class ControlAI {
    const MODEL = 'gemini-2.5-flash-lite';
    const LOW_SCORE_THRESHOLD = 40;
    const AUTO_RESPONDER_USER_ID = 2;

    private function tableColumnExists($table, $column) {
        $db = config::getConnexion();
        try {
            $req = $db->prepare("
                SELECT COUNT(*)
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = :table_name
                AND COLUMN_NAME = :column_name
            ");
            $req->execute(['table_name' => $table, 'column_name' => $column]);
            return (int)$req->fetchColumn() > 0;
        } catch (Exception $e) { return false; }
    }

    public function scoreStorageReady() {
        return $this->tableColumnExists('post', 'ai_score') && $this->tableColumnExists('reply', 'ai_score');
    }

    private function getApiKey() {
        $key = getenv('GOOGLE_API_KEY') ?: getenv('GEMINI_API_KEY');
        if ($key) return trim($key);

        $envPath = __DIR__ . '/../.env';
        if (!is_file($envPath)) return '';

        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (in_array($name, ['GOOGLE_API_KEY', 'GEMINI_API_KEY'], true)) return $value;
        }

        return '';
    }

    public function scoreContent($type, $title, $content) {
        $result = $this->debugScoreContent($type, $title, $content);
        return $result['score'];
    }

    private function autoResponderPromptPath() {
        return dirname(__DIR__) . '/ai_responder_prompt.txt';
    }

    public function defaultAutoResponderPrompt() {
        return "Tu es un modérateur du forum Green Assurance. Réponds brièvement au post si tu peux aider, avec un ton calme et utile. Termine toujours par un rappel court invitant l'utilisateur à respecter les règles du forum. Ne donne pas de conseil dangereux, illégal, médical ou financier personnalisé.";
    }

    public function getAutoResponderPrompt() {
        $path = $this->autoResponderPromptPath();
        if (is_file($path)) {
            $prompt = trim((string)file_get_contents($path));
            if ($prompt !== '') return $prompt;
        }
        return $this->defaultAutoResponderPrompt();
    }

    public function updateAutoResponderPrompt($prompt) {
        $prompt = trim($prompt);
        if ($prompt === '') $prompt = $this->defaultAutoResponderPrompt();
        return file_put_contents($this->autoResponderPromptPath(), $prompt) !== false;
    }

    public function generateAutoReply($title, $content) {
        $apiKey = $this->getApiKey();
        if (!$apiKey) return null;

        $prompt = $this->getAutoResponderPrompt()
            . "\n\nPost a moderer :\n"
            . "Titre : " . $title . "\n"
            . "Contenu : " . $content . "\n\n"
            . "Retourne uniquement la reponse a publier, en 2 a 4 phrases maximum.";

        foreach ([true, false] as $useSafetySettings) {
            $attempt = $this->callGemini($prompt, false, $useSafetySettings, 180);
            $reply = $this->cleanAutoReply($attempt['response_text'] ?? '');
            if ($reply !== '') return $reply;
        }

        return null;
    }

    private function cleanAutoReply($text) {
        $text = trim(strip_tags((string)$text));
        $text = preg_replace('/^```(?:text)?|```$/m', '', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));
        if ($text === '') return '';
        return mb_substr($text, 0, 700);
    }

    public function debugScoreContent($type, $title, $content) {
        $apiKey = $this->getApiKey();
        $result = [
            'model' => self::MODEL,
            'api_key_present' => $apiKey !== '',
            'score' => null,
            'error' => '',
            'attempts' => [],
        ];
        if (!$apiKey) {
            $result['error'] = 'Missing GOOGLE_API_KEY or GEMINI_API_KEY.';
            return $result;
        }

        $attempts = [
            ['prompt' => $this->buildPrompt($type, $title, $content, true), 'json' => true, 'safety' => true],
            ['prompt' => $this->buildPrompt($type, $title, $content, false), 'json' => false, 'safety' => true],
            ['prompt' => $this->buildPrompt($type, $title, $content, false), 'json' => false, 'safety' => false],
        ];

        foreach ($attempts as $attemptConfig) {
            $attempt = $this->callGemini($attemptConfig['prompt'], $attemptConfig['json'], $attemptConfig['safety']);
            $attempt['parsed_score'] = $this->extractScore($attempt['response_text'] ?? '', $attempt['raw_response'] ?? '');
            $result['attempts'][] = $attempt;

            if ($attempt['parsed_score'] !== null) {
                $result['score'] = $attempt['parsed_score'];
                return $result;
            }
        }

        $result['error'] = 'Gemini did not return a parseable score.';
        return $result;
    }

    private function buildPrompt($type, $title, $content, $jsonMode) {
        $format = $jsonMode
            ? 'Return exactly this JSON shape and nothing else: {"score": 0}'
            : 'Return only one integer from 0 to 100 and nothing else.';

        return "You are a forum moderation scorer. Score the following $type from 0 to 100. "
            . "0 means toxic, spam, insulting, promotional, or unsafe. "
            . "100 means healthy, respectful, useful, and non-spam. "
            . "Consider spam, toxicity, insults, promotion, harassment, and irrelevant content. "
            . $format . "\n\n"
            . "Title: " . $title . "\n"
            . "Content: " . $content;
    }

    private function callGemini($prompt, $jsonMode, $useSafetySettings = true, $maxOutputTokens = 32) {
        $request = [
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]],
            'generationConfig' => [
                'temperature' => 0,
                'maxOutputTokens' => $maxOutputTokens,
                'responseMimeType' => $jsonMode ? 'application/json' : 'text/plain',
            ],
        ];

        if ($useSafetySettings) {
            $request['safetySettings'] = [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
            ];
        }

        $payload = json_encode($request);

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODEL . ':generateContent';
        $response = null;
        $status = 0;
        $transportError = '';
        $apiKey = $this->getApiKey();

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'x-goog-api-key: ' . $apiKey,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_TIMEOUT => 12,
            ]);

            $response = curl_exec($ch);
            if ($response === false) $transportError = curl_error($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => "x-goog-api-key: $apiKey\r\nContent-Type: application/json\r\n",
                    'content' => $payload,
                    'timeout' => 12,
                    'ignore_errors' => true,
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            $statusLine = $http_response_header[0] ?? '';
            if (preg_match('/\s(\d{3})\s/', $statusLine, $m)) $status = (int)$m[1];
        }

        $data = json_decode($response, true);
        $text = $this->candidateText($data);

        return [
            'http_status' => $status,
            'transport_error' => $transportError,
            'response_text' => $text,
            'raw_response' => $response ?: '',
            'prompt' => $prompt,
            'used_safety_settings' => $useSafetySettings,
        ];
    }

    private function candidateText($data) {
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        $text = '';
        foreach ($parts as $part) {
            if (isset($part['text'])) $text .= $part['text'] . "\n";
        }
        return trim($text);
    }

    private function extractScore($text, $rawResponse = '') {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?|```$/m', '', $text);
        $decoded = json_decode(trim($text), true);
        if (is_array($decoded)) {
            if (isset($decoded['score']) && is_numeric($decoded['score'])) {
                return max(0, min(100, (int)round($decoded['score'])));
            }
            if (isset($decoded[0]['score']) && is_numeric($decoded[0]['score'])) {
                return max(0, min(100, (int)round($decoded[0]['score'])));
            }
        }

        if (preg_match('/"score"\s*:\s*(100|[0-9]{1,2})\b/i', $text, $matches)) {
            return max(0, min(100, (int)$matches[1]));
        }
        if (preg_match('/\b(100|[0-9]{1,2})\b/', $text, $matches)) {
            return max(0, min(100, (int)$matches[1]));
        }

        $rawDecoded = json_decode($rawResponse, true);
        $finishReason = $rawDecoded['candidates'][0]['finishReason'] ?? '';
        if ($finishReason && preg_match('/\b(100|[0-9]{1,2})\b/', $finishReason, $matches)) {
            return max(0, min(100, (int)$matches[1]));
        }

        return null;
    }

    public function scorePost($postId, $title, $content) {
        if (!$this->scoreStorageReady()) return null;
        $score = $this->scoreContent('post', $title, $content);
        if ($score !== null) $this->savePostScore($postId, $score);
        return $score;
    }

    public function scoreReply($replyId, $content) {
        if (!$this->scoreStorageReady()) return null;
        $score = $this->scoreContent('reply', '', $content);
        if ($score !== null) $this->saveReplyScore($replyId, $score);
        return $score;
    }

    private function savePostScore($postId, $score) {
        $db = config::getConnexion();
        $statutSql = $score < self::LOW_SCORE_THRESHOLD ? ", statut = IF(statut = 'supprime', 'supprime', 'masque')" : "";
        $req = $db->prepare("UPDATE post SET ai_score = :score $statutSql WHERE id = :id");
        $req->execute(['score' => $score, 'id' => $postId]);
    }

    private function saveReplyScore($replyId, $score) {
        $db = config::getConnexion();
        $statutSql = $score < self::LOW_SCORE_THRESHOLD ? ", statut = IF(statut = 'supprime', 'supprime', 'masque')" : "";
        $req = $db->prepare("UPDATE reply SET ai_score = :score $statutSql WHERE id = :id");
        $req->execute(['score' => $score, 'id' => $replyId]);
    }

    public function getScoreItems($includePosts = true, $includeReplies = true, $sort = 'asc', $moderationOnly = false) {
        if (!$this->scoreStorageReady()) return [];

        $db = config::getConnexion();
        $sort = strtolower($sort) === 'desc' ? 'DESC' : 'ASC';
        $parts = [];

        if ($includePosts) {
            $where = $moderationOnly
                ? "p.ai_score IS NOT NULL AND p.ai_score < " . self::LOW_SCORE_THRESHOLD . " AND p.statut = 'masque'"
                : "p.ai_score IS NOT NULL";
            $parts[] = "
                SELECT 'post' AS target_type, p.id, p.id AS post_id, p.titre AS title, p.contenu AS content,
                p.ai_score, p.statut, p.created_at, u.nom, u.prenom
                FROM post p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE $where
            ";
        }

        if ($includeReplies) {
            $where = $moderationOnly
                ? "r.ai_score IS NOT NULL AND r.ai_score < " . self::LOW_SCORE_THRESHOLD . " AND r.statut = 'masque'"
                : "r.ai_score IS NOT NULL";
            $parts[] = "
                SELECT 'reply' AS target_type, r.id, r.post_id AS post_id, p.titre AS title, r.contenu AS content,
                       r.ai_score, r.statut, r.created_at, u.nom, u.prenom
                FROM reply r
                LEFT JOIN post p ON p.id = r.post_id
                LEFT JOIN users u ON u.id = r.user_id
                WHERE $where
            ";
        }

        if (empty($parts)) return [];

        $sql = implode(" UNION ALL ", $parts) . " ORDER BY ai_score $sort, created_at DESC";
        return $db->query($sql)->fetchAll();
    }
}
