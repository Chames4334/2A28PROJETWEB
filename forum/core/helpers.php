<?php

declare(strict_types=1);

/**
 * Échappement HTML pour les vues.
 */
function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Chemin relatif vers les fichiers statiques dans public/ (scripts d’entrée dans views/).
 */
function forum_asset_url(string $relativeFile): string
{
    return '../public/' . ltrim(str_replace('\\', '/', $relativeFile), '/');
}

/**
 * Génère ou retourne le jeton CSRF de session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

/**
 * Vérifie le jeton CSRF (formulaires POST).
 */
function csrf_verify(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Redirection HTTP puis fin de script.
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Utilisateur connecté : id ou null.
 */
function current_user_id(): ?int
{
    if (!empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }
    return null;
}

/**
 * Extrait un extrait de texte brut pour la liste des sujets.
 */
function excerpt(string $htmlOrText, int $maxLen = 180): string
{
    $plain = trim(preg_replace('/\s+/', ' ', strip_tags($htmlOrText)));
    if (mb_strlen($plain) <= $maxLen) {
        return $plain;
    }
    return mb_substr($plain, 0, $maxLen) . '…';
}

/**
 * Affiche une vue dans views/ (chemins avec slash : front_office/post/index, back_office/admin/dashboard).
 *
 * @param array<string, mixed> $data
 */
function view(string $relativePath, array $data = []): void
{
    global $currentForumUser, $currentUserIsAdmin;
    if (!array_key_exists('layoutMainClass', $data)) {
        $data['layoutMainClass'] = '';
    }
    extract($data, EXTR_SKIP);
    /* Chemin depuis ce fichier (core/) — ne dépend pas de la constante FORUM_VIEWS */
    $path = dirname(__DIR__) . '/views/' . $relativePath . '.php';
    if (!is_file($path)) {
        throw new RuntimeException('Vue introuvable : ' . $relativePath);
    }
    require $path;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type: string, message: string}|null */
function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return is_array($f) ? $f : null;
}
