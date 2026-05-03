<?php
try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS gestion_conges");
    $pdo->exec("USE gestion_conges");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS Employe (
        id_employe INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        solde_total INT DEFAULT 30
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    // Migration : Ajouter la colonne email si elle n'existe pas
    try {
        $pdo->exec("ALTER TABLE Employe ADD COLUMN email VARCHAR(100) NULL AFTER prenom");
        echo "✅ Colonne 'email' ajoutée à la table Employe<br>";
        
        // Mettre à jour avec des données fictives pour le test
        $pdo->exec("UPDATE Employe SET email = 'john.doe@example.com' WHERE id_employe = 1");
        $pdo->exec("UPDATE Employe SET email = 'jane.smith@example.com' WHERE id_employe = 2");
        $pdo->exec("UPDATE Employe SET email = 'paul.martin@example.com' WHERE id_employe = 3");
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') === false) {
            throw $e;
        }
    }
    echo "✅ Table 'Employe' opérationnelle.<br>";

    // 3. Insérer des employés de test si vide
    $check = $pdo->query("SELECT COUNT(*) FROM Employe")->fetchColumn();
    if ($check == 0) {
        $pdo->exec("INSERT INTO Employe (nom, prenom, email, solde_total) VALUES 
            ('Doe', 'John', 'john.doe@example.com', 30),
            ('Smith', 'Jane', 'jane.smith@example.com', 30),
            ('Martin', 'Paul', 'paul.martin@example.com', 30)");
        echo "✅ Données de test insérées dans 'Employe'.<br>";
    }

    // Ajouter les colonnes manquantes à la table Conge
    try {
        $pdo->exec("ALTER TABLE Conge ADD COLUMN date_traitement DATE NULL");
        echo "✅ Colonne 'date_traitement' ajoutée<br>";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "ℹ️ La colonne 'date_traitement' existe déjà<br>";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE Conge ADD COLUMN decision VARCHAR(20) NULL");
        echo "✅ Colonne 'decision' ajoutée<br>";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "ℹ️ La colonne 'decision' existe déjà<br>";
        } else {
            throw $e;
        }
    }
    
    try {
        $pdo->exec("ALTER TABLE Conge ADD COLUMN commentaire_traitement TEXT NULL");
        echo "✅ Colonne 'commentaire_traitement' ajoutée<br>";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "ℹ️ La colonne 'commentaire_traitement' existe déjà<br>";
        } else {
            throw $e;
        }
    }
    
    // Mettre à jour les données existantes (optionnel)
    $pdo->exec("UPDATE Conge SET date_traitement = NULL WHERE date_traitement IS NULL");
    $pdo->exec("UPDATE Conge SET decision = 'en_attente' WHERE decision IS NULL AND statut = 'en_attente'");
    
    echo "<br><strong style='color: green;'>✅ Base de données mise à jour avec succès !</strong><br>";
    echo "<hr>";
    echo "<a href='index.php?page=home' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Accéder à l'application</a>";
    
} catch(PDOException $e) {
    die("<strong style='color: red;'>❌ Erreur :</strong> " . $e->getMessage());
}
?>