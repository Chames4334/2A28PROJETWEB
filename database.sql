-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_conges;
USE gestion_conges;

-- Table CONGE
CREATE TABLE IF NOT EXISTS Conge (
    id_conge INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    type_conge VARCHAR(50) NOT NULL,
    motif TEXT NOT NULL,
    statut VARCHAR(20) DEFAULT 'en_attente',
    date_demande DATE NOT NULL,
    id_employe INT NOT NULL
);

-- Table TRAITEMENT CONGE
CREATE TABLE IF NOT EXISTS TraitementConge (
    id_traitement INT AUTO_INCREMENT PRIMARY KEY,
    date_traitement DATE,
    decision VARCHAR(20),
    commentaire TEXT,
    id_conge INT,
    FOREIGN KEY (id_conge) REFERENCES Conge(id_conge) ON DELETE CASCADE
);
