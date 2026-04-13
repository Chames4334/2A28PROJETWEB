<?php

declare(strict_types=1);

/**
 * Sujets du forum (table post).
 */
class Post
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Liste des sujets actifs : épinglés en premier, puis du plus récent au plus ancien.
     * Inclut nom/prénom auteur et nombre de réponses actives.
     *
     * @return list<array<string, mixed>>
     */
    public function getAllActiveWithMeta(): array
    {
        $sql = 'SELECT p.id, p.user_id, p.titre, p.contenu, p.is_pinned, p.statut, p.created_at, p.updated_at,
                       u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       (SELECT COUNT(*) FROM reply r
                        WHERE r.post_id = p.id AND r.statut = \'actif\') AS reply_count
                FROM post p
                INNER JOIN users u ON u.id = p.user_id
                WHERE p.statut = \'actif\'
                ORDER BY p.is_pinned DESC, p.created_at DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findActiveById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
             FROM post p
             INNER JOIN users u ON u.id = p.user_id
             WHERE p.id = :id AND p.statut = \'actif\'
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Sujets actifs d’un utilisateur (pour « Mes sujets »).
     *
     * @return list<array<string, mixed>>
     */
    public function getActiveByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*,
                    (SELECT COUNT(*) FROM reply r
                     WHERE r.post_id = p.id AND r.statut = \'actif\') AS reply_count
             FROM post p
             WHERE p.user_id = :user_id AND p.statut = \'actif\'
             ORDER BY p.is_pinned DESC, p.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(int $userId, string $titre, string $contenu): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO post (user_id, titre, contenu, is_pinned, statut)
             VALUES (:user_id, :titre, :contenu, 0, \'actif\')'
        );
        $stmt->execute([
            'user_id' => $userId,
            'titre'   => $titre,
            'contenu' => $contenu,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateContent(int $postId, int $authorUserId, string $titre, string $contenu): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE post SET titre = :titre, contenu = :contenu
             WHERE id = :id AND user_id = :user_id AND statut = \'actif\''
        );
        $stmt->execute([
            'titre'    => $titre,
            'contenu'  => $contenu,
            'id'       => $postId,
            'user_id'  => $authorUserId,
        ]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Masque le sujet (soft delete) si l’auteur correspond.
     */
    public function softDeleteByAuthor(int $postId, int $authorUserId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE post SET statut = \'masque\'
             WHERE id = :id AND user_id = :user_id AND statut = \'actif\''
        );
        $stmt->execute(['id' => $postId, 'user_id' => $authorUserId]);
        return $stmt->rowCount() > 0;
    }

    public function belongsToUser(int $postId, int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM post WHERE id = :id AND user_id = :user_id AND statut = \'actif\' LIMIT 1'
        );
        $stmt->execute(['id' => $postId, 'user_id' => $userId]);
        return (bool) $stmt->fetchColumn();
    }
}
