-- ── Création de la base de données ────────────────────────────
CREATE DATABASE IF NOT EXISTS gestion_conges;
USE gestion_conges;

-- ── Table CONGE ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `Conge` (
    `id_conge` INT AUTO_INCREMENT PRIMARY KEY,
    `date_debut` DATE NOT NULL,
    `date_fin` DATE NOT NULL,
    `type_conge` VARCHAR(50) NOT NULL,
    `motif` TEXT NOT NULL,
    `statut` VARCHAR(20) DEFAULT 'en_attente',
    `date_demande` DATE NOT NULL,
    `id_employe` INT NOT NULL,
    INDEX `idx_conge_statut` (`statut`),
    INDEX `idx_conge_date_debut` (`date_debut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table TRAITEMENT CONGE ────────────────────────────────────
CREATE TABLE IF NOT EXISTS `TraitementConge` (
    `id_traitement` INT AUTO_INCREMENT PRIMARY KEY,
    `date_traitement` DATE,
    `decision` VARCHAR(20),
    `commentaire` TEXT,
    `id_conge` INT,
    INDEX `idx_tc_conge_id` (`id_conge`),
    CONSTRAINT `fk_tc_conge` FOREIGN KEY (`id_conge`) REFERENCES `Conge`(`id_conge`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Données d'exemple ─────────────────────────────────────────
INSERT INTO `Conge` (`date_debut`, `date_fin`, `type_conge`, `motif`, `statut`, `date_demande`, `id_employe`) VALUES
('2025-05-01', '2025-05-10', 'Congé payé', 'Vacances d\'été', 'en_attente', '2025-04-15', 1),
('2025-06-01', '2025-06-05', 'Congé maladie', 'Arrêt médical', 'approuvé', '2025-05-25', 2),
('2025-07-15', '2025-07-20', 'Congé payé', 'Vacances familiales', 'en_attente', '2025-06-20', 3);

INSERT INTO `TraitementConge` (`date_traitement`, `decision`, `commentaire`, `id_conge`) VALUES
('2025-04-16', 'en_attente', 'Document reçu, en cours de vérification', 1),
('2025-05-26', 'approuvé', 'Demande approuvée', 2),
('2025-06-21', 'en_attente', 'En attente de confirmation managériale', 3);
