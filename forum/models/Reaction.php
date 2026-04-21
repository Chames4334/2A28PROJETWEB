<?php

declare(strict_types=1);

/**
 * Likes / dislikes (table reaction).
 */
class Reaction
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Compteurs pour le sujet (réactions sur le post uniquement, reply_id NULL).
     *
     * @return array{likes: int, dislikes: int}
     */
    public function getPostCounts(int $postId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT type_reaction, COUNT(*) AS c FROM reaction
             WHERE post_id = :pid AND reply_id IS NULL
             GROUP BY type_reaction'
        );
        $stmt->execute(['pid' => $postId]);
        $likes = 0;
        $dislikes = 0;
        while ($row = $stmt->fetch()) {
            if ($row['type_reaction'] === 'like') {
                $likes = (int) $row['c'];
            } elseif ($row['type_reaction'] === 'dislike') {
                $dislikes = (int) $row['c'];
            }
        }
        return ['likes' => $likes, 'dislikes' => $dislikes];
    }

    /**
     * @return array{likes: int, dislikes: int}
     */
    public function getReplyCounts(int $replyId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT type_reaction, COUNT(*) AS c FROM reaction
             WHERE reply_id = :rid
             GROUP BY type_reaction'
        );
        $stmt->execute(['rid' => $replyId]);
        $likes = 0;
        $dislikes = 0;
        while ($row = $stmt->fetch()) {
            if ($row['type_reaction'] === 'like') {
                $likes = (int) $row['c'];
            } elseif ($row['type_reaction'] === 'dislike') {
                $dislikes = (int) $row['c'];
            }
        }
        return ['likes' => $likes, 'dislikes' => $dislikes];
    }

    public function getUserPostReaction(int $userId, int $postId): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT type_reaction FROM reaction
             WHERE user_id = :u AND post_id = :p AND reply_id IS NULL LIMIT 1'
        );
        $stmt->execute(['u' => $userId, 'p' => $postId]);
        $t = $stmt->fetchColumn();
        return $t !== false ? (string) $t : null;
    }

    public function getUserReplyReaction(int $userId, int $replyId): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT type_reaction FROM reaction
             WHERE user_id = :u AND reply_id = :r LIMIT 1'
        );
        $stmt->execute(['u' => $userId, 'r' => $replyId]);
        $t = $stmt->fetchColumn();
        return $t !== false ? (string) $t : null;
    }

    /**
     * Même type → retire la réaction ; autre type → met à jour ; aucune → insère.
     */
    public function applyToPost(int $userId, int $postId, string $type): void
    {
        if (!in_array($type, ['like', 'dislike'], true)) {
            return;
        }
        $stmt = $this->pdo->prepare(
            'SELECT id, type_reaction FROM reaction
             WHERE user_id = :u AND post_id = :p AND reply_id IS NULL LIMIT 1'
        );
        $stmt->execute(['u' => $userId, 'p' => $postId]);
        $row = $stmt->fetch();
        if ($row === false) {
            $ins = $this->pdo->prepare(
                'INSERT INTO reaction (user_id, post_id, reply_id, type_reaction)
                 VALUES (:u, :p, NULL, :t)'
            );
            $ins->execute(['u' => $userId, 'p' => $postId, 't' => $type]);
            return;
        }
        if ((string) $row['type_reaction'] === $type) {
            $del = $this->pdo->prepare('DELETE FROM reaction WHERE id = :id');
            $del->execute(['id' => $row['id']]);
            return;
        }
        $up = $this->pdo->prepare('UPDATE reaction SET type_reaction = :t WHERE id = :id');
        $up->execute(['t' => $type, 'id' => $row['id']]);
    }

    public function applyToReply(int $userId, int $replyId, string $type): void
    {
        if (!in_array($type, ['like', 'dislike'], true)) {
            return;
        }
        $stmt = $this->pdo->prepare(
            'SELECT id, type_reaction FROM reaction
             WHERE user_id = :u AND reply_id = :r LIMIT 1'
        );
        $stmt->execute(['u' => $userId, 'r' => $replyId]);
        $row = $stmt->fetch();
        if ($row === false) {
            $ins = $this->pdo->prepare(
                'INSERT INTO reaction (user_id, post_id, reply_id, type_reaction)
                 VALUES (:u, NULL, :r, :t)'
            );
            $ins->execute(['u' => $userId, 'r' => $replyId, 't' => $type]);
            return;
        }
        if ((string) $row['type_reaction'] === $type) {
            $del = $this->pdo->prepare('DELETE FROM reaction WHERE id = :id');
            $del->execute(['id' => $row['id']]);
            return;
        }
        $up = $this->pdo->prepare('UPDATE reaction SET type_reaction = :t WHERE id = :id');
        $up->execute(['t' => $type, 'id' => $row['id']]);
    }

    /**
     * Données agrégées pour la page sujet (évite N+1).
     *
     * @param list<int> $replyIds
     * @return array{
     *   post: array{likes: int, dislikes: int, user: ?string},
     *   replies: array<int, array{likes: int, dislikes: int, user: ?string}>
     * }
     */
    public function summarizeForPostPage(int $postId, ?int $userId, array $replyIds): array
    {
        $postCounts = $this->getPostCounts($postId);
        $postUser = $userId !== null ? $this->getUserPostReaction($userId, $postId) : null;

        $replies = [];
        foreach ($replyIds as $rid) {
            $replies[$rid] = ['likes' => 0, 'dislikes' => 0, 'user' => null];
        }

        if ($replyIds !== []) {
            $placeholders = implode(',', array_fill(0, count($replyIds), '?'));
            $stmt = $this->pdo->prepare(
                "SELECT reply_id, type_reaction, COUNT(*) AS c FROM reaction
                 WHERE reply_id IN ($placeholders)
                 GROUP BY reply_id, type_reaction"
            );
            $stmt->execute(array_values($replyIds));
            while ($row = $stmt->fetch()) {
                $rid = (int) $row['reply_id'];
                if (!isset($replies[$rid])) {
                    continue;
                }
                if ($row['type_reaction'] === 'like') {
                    $replies[$rid]['likes'] = (int) $row['c'];
                } else {
                    $replies[$rid]['dislikes'] = (int) $row['c'];
                }
            }

            if ($userId !== null) {
                $stmt = $this->pdo->prepare(
                    "SELECT reply_id, type_reaction FROM reaction
                     WHERE user_id = ? AND reply_id IN ($placeholders)"
                );
                $params = array_merge([$userId], array_values($replyIds));
                $stmt->execute($params);
                while ($row = $stmt->fetch()) {
                    $rid = (int) $row['reply_id'];
                    if (isset($replies[$rid])) {
                        $replies[$rid]['user'] = (string) $row['type_reaction'];
                    }
                }
            }
        }

        return [
            'post'    => array_merge($postCounts, ['user' => $postUser]),
            'replies' => $replies,
        ];
    }
}
