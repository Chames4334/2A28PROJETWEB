<?php
/**
 * Connexion PDO à la base insurance_db.
 * Ajustez l'hôte si votre MySQL n'est pas en local.
 */

declare(strict_types=1);

return [
    'dsn'      => 'mysql:host=127.0.0.1;dbname=insurance_db;charset=utf8mb4',
    'user'     => 'root',
    'password' => '',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
