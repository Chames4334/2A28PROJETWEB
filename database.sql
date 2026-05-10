-- Base de données pour la gestion des congés et du planning intelligent

CREATE TABLE IF NOT EXISTS `Employe` (
    `id_employe` INT AUTO_INCREMENT PRIMARY KEY,
    `nom` VARCHAR(50) NOT NULL,
    `prenom` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `solde_total` INT DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `Employe` (`id_employe`, `nom`, `prenom`, `email`, `solde_total`) VALUES
(1, 'Doe', 'John', 'john.doe@example.com', 30),
(2, 'Smith', 'Jane', 'jane.smith@example.com', 30),
(3, 'Martin', 'Paul', 'paul.martin@example.com', 30);

CREATE TABLE IF NOT EXISTS `Conge` (
    `id_conge` INT AUTO_INCREMENT PRIMARY KEY,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `type_conge` VARCHAR(50) NOT NULL,
    `motif` TEXT NOT NULL,
    `statut` VARCHAR(20) DEFAULT 'en_attente',
    `date_demande` DATE NOT NULL,
    `id_employe` INT NOT NULL,
    `date_traitement` DATE NULL,
    `decision` VARCHAR(20) NULL,
    `commentaire_traitement` TEXT NULL,
    INDEX `idx_conge_statut` (`statut`),
    INDEX `idx_conge_date_debut` (`date_debut`),
    FOREIGN KEY (`id_employe`) REFERENCES `Employe`(`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `Conge` (`date_debut`, `date_fin`, `type_conge`, `motif`, `statut`, `date_demande`, `id_employe`, `date_traitement`, `decision`, `commentaire_traitement`) VALUES
('2025-05-01', '2025-05-10', 'Congé payé', 'Vacances d\'été', 'en_attente', '2025-04-15', 1, '2025-04-16', 'en_attente', 'Document reçu, en cours de vérification'),
('2025-06-01', '2025-06-05', 'Congé maladie', 'Arrêt médical', 'approuvé', '2025-05-25', 2, '2025-05-26', 'approuvé', 'Demande approuvée'),
('2025-07-15', '2025-07-20', 'Congé payé', 'Vacances familiales', 'en_attente', '2025-06-20', 3, '2025-06-21', 'en_attente', 'En attente de confirmation managériale');

-- Table des Disponibilités (Contraintes employés)
CREATE TABLE IF NOT EXISTS `Disponibilite` (
    `id_dispo` INT AUTO_INCREMENT PRIMARY KEY,
    `id_employe` INT NOT NULL,
    `jour_semaine` INT NOT NULL, -- 1 (Lundi) à 7 (Dimanche)
    `est_disponible` TINYINT(1) DEFAULT 1,
    `notes` TEXT,
    FOREIGN KEY (`id_employe`) REFERENCES `Employe`(`id_employe`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des Plannings générés
CREATE TABLE IF NOT EXISTS `Planning` (
    `id_planning` INT AUTO_INCREMENT PRIMARY KEY,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `data_json` JSON NOT NULL, -- Stockage du planning détaillé
    `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de quelques disponibilités par défaut
INSERT IGNORE INTO `Disponibilite` (`id_employe`, `jour_semaine`, `est_disponible`) VALUES
(1, 6, 0), (1, 7, 0), -- John ne travaille pas le weekend
(2, 6, 0), (2, 7, 0),
(3, 6, 0), (3, 7, 0);