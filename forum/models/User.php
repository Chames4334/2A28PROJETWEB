<?php

declare(strict_types=1);

/**
 * Accès aux utilisateurs (table users).
 */
class User
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nom, prenom, email, phone, address, status, created_at, updated_at
             FROM users WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nom, prenom, email, password_hash, status FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Nom affiché : nom + prénom (schéma colonnes users).
     */
    public static function displayName(array $user): string
    {
        $n = trim((string) ($user['nom'] ?? ''));
        $p = trim((string) ($user['prenom'] ?? ''));
        $full = trim($n . ' ' . $p);
        return $full !== '' ? $full : ('Utilisateur #' . (int) ($user['id'] ?? 0));
    }

    public function create(string $nom, string $prenom, string $email, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (nom, prenom, email, password_hash, status)
             VALUES (:nom, :prenom, :email, :password_hash, \'active\')'
        );
        $stmt->execute([
            'nom'            => $nom,
            'prenom'         => $prenom,
            'email'          => $email,
            'password_hash'  => $passwordHash,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Rôle administrateur : role_id = 1 dans user_roles (table roles : admin en premier).
     */
    public function hasAdminRole(int $userId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM user_roles WHERE user_id = :uid AND role_id = 1 LIMIT 1'
        );
        $stmt->execute(['uid' => $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}
