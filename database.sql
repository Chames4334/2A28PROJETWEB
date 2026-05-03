-- Création de la table Employe pour gérer les soldes
CREATE TABLE IF NOT EXISTS `Employe` (
    `id_employe` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(50) NOT NULL,
    `prenom` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NULL,
    `solde_total` INT DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `Employe` (`id_employe`, `nom`, `prenom`, `email`, `solde_total`) VALUES
(1, 'Doe', 'John', 'john.doe@example.com', 30),
(2, 'Smith', 'Jane', 'jane.smith@example.com', 30),
(3, 'Martin', 'Paul', 'paul.martin@example.com', 30);

-- Nouvelle structure de la table CONGE avec champs de traitement intégrés
CREATE TABLE IF NOT EXISTS `Conge` (
    `id_conge` INT AUTO_INCREMENT PRIMARY KEY,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `type_conge` VARCHAR(50) NOT NULL,
    `motif` TEXT NOT NULL,
    `statut` VARCHAR(20) DEFAULT 'en_attente',
    `date_demande` DATE NOT NULL,
    `id_employe` INT NOT NULL,
    -- Champs de traitement intégrés
    `date_traitement` DATE NULL,
    `decision` VARCHAR(20) NULL,
    `commentaire_traitement` TEXT NULL,
    INDEX `idx_conge_statut` (`statut`),
    INDEX `idx_conge_date_debut` (`date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Mettre à jour les données existantes
INSERT INTO `Conge` (`date_debut`, `date_fin`, `type_conge`, `motif`, `statut`, `date_demande`, `id_employe`, `date_traitement`, `decision`, `commentaire_traitement`) VALUES
('2025-05-01', '2025-05-10', 'Congé payé', 'Vacances d\'été', 'en_attente', '2025-04-15', 1, '2025-04-16', 'en_attente', 'Document reçu, en cours de vérification'),
('2025-06-01', '2025-06-05', 'Congé maladie', 'Arrêt médical', 'approuvé', '2025-05-25', 2, '2025-05-26', 'approuvé', 'Demande approuvée'),
('2025-07-15', '2025-07-20', 'Congé payé', 'Vacances familiales', 'en_attente', '2025-06-20', 3, '2025-06-21', 'en_attente', 'En attente de confirmation managériale');

-- Supprimer l'ancienne table (optionnel, après sauvegarde)
-- DROP TABLE IF EXISTS TraitementConge;