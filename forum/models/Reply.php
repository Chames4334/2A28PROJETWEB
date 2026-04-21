<?php

declare(strict_types=1);

/**
 * Réponses aux sujets (table reply).
 * Les réponses imbriquées (parent_reply_id) ne sont pas gérées côté UI pour l’instant.
 */
class Reply
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Réponses actives directement sous le sujet (pas de fil de réponses pour l’instant).
     *
     * @return list<array<string, mixed>>
     */
    public function getActiveTopLevelByPostId(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom
             FROM reply r
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.post_id = :post_id
               AND r.statut = \'actif\'
               AND r.parent_reply_id IS NULL
             ORDER BY r.created_at ASC'
        );
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll();
    }

    public function create(int $postId, int $userId, string $contenu): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO reply (post_id, user_id, parent_reply_id, contenu, statut)
             VALUES (:post_id, :user_id, NULL, :contenu, \'actif\')'
        );
        $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'contenu' => $contenu,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function softDeleteByAuthor(int $replyId, int $authorUserId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reply SET statut = \'masque\'
             WHERE id = :id AND user_id = :user_id AND statut = \'actif\''
        );
        $stmt->execute(['id' => $replyId, 'user_id' => $authorUserId]);
        return $stmt->rowCount() > 0;
    }

    public function findActiveById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM reply WHERE id = :id AND statut = \'actif\' LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateContentByAuthor(int $replyId, int $userId, string $contenu): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reply SET contenu = :contenu
             WHERE id = :id AND user_id = :user_id AND statut = \'actif\''
        );
        $stmt->execute(['contenu' => $contenu, 'id' => $replyId, 'user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllForAdmin(): array
    {
        $sql = 'SELECT r.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                       p.titre AS post_titre, p.id AS post_ref_id
                FROM reply r
                INNER JOIN users u ON u.id = r.user_id
                INNER JOIN post p ON p.id = r.post_id
                ORDER BY r.created_at DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findByIdForAdmin(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, u.nom AS auteur_nom, u.prenom AS auteur_prenom,
                    p.titre AS post_titre, p.id AS post_ref_id
             FROM reply r
             INNER JOIN users u ON u.id = r.user_id
             INNER JOIN post p ON p.id = r.post_id
             WHERE r.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function adminUpdateContent(int $replyId, string $contenu): bool
    {
        $stmt = $this->pdo->prepare('UPDATE reply SET contenu = :contenu WHERE id = :id');
        $stmt->execute(['contenu' => $contenu, 'id' => $replyId]);
        return $stmt->rowCount() > 0;
    }

    public function adminSoftDelete(int $replyId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE reply SET statut = \'masque\' WHERE id = :id AND statut <> \'masque\''
        );
        $stmt->execute(['id' => $replyId]);
        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM reply')->fetchColumn();
    }

    public function countByStatut(string $statut): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM reply WHERE statut = :statut');
        $stmt->execute(['statut' => $statut]);
        return (int) $stmt->fetchColumn();
    }
}
