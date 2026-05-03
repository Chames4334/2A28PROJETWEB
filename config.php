<?php
// config.php
class config {
    private static $connexion = null;

    public static function getConnexion() {
        if (self::$connexion === null) {
            try {
                self::$connexion = new PDO(
                    'mysql:host=localhost;dbname=insurance_db;charset=utf8mb4',
                    'root',
                    'root',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                die('Connexion échouée : ' . $e->getMessage());
            }
        }
        return self::$connexion;
    }
}
