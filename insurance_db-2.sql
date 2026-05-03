-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 03, 2026 at 11:03 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `insurance_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assurance_auto`
--

CREATE TABLE `assurance_auto` (
  `id` int UNSIGNED NOT NULL,
  `offre_id` int UNSIGNED NOT NULL,
  `marque` varchar(100) NOT NULL,
  `modele` varchar(100) NOT NULL,
  `annee` year NOT NULL,
  `immatriculation` varchar(30) NOT NULL,
  `puissance_cv` int UNSIGNED DEFAULT NULL,
  `valeur_vehicule` decimal(12,2) DEFAULT NULL,
  `type_carburant` enum('essence','diesel','electrique','hybride','gpl') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assurance_habitation`
--

CREATE TABLE `assurance_habitation` (
  `id` int UNSIGNED NOT NULL,
  `offre_id` int UNSIGNED NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `localisation` varchar(150) DEFAULT NULL,
  `surface_m2` decimal(8,2) DEFAULT NULL,
  `type_logement` enum('appartement','maison','villa','studio','local_commercial') DEFAULT NULL,
  `valeur_bien` decimal(12,2) DEFAULT NULL,
  `meuble` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assurance_sante`
--

CREATE TABLE `assurance_sante` (
  `id` int UNSIGNED NOT NULL,
  `offre_id` int UNSIGNED NOT NULL,
  `age_limit` tinyint UNSIGNED DEFAULT NULL,
  `type_couverture` enum('individuelle','familiale','entreprise') NOT NULL,
  `plafond_remboursement` decimal(10,2) DEFAULT NULL,
  `dentaire` tinyint(1) NOT NULL DEFAULT '0',
  `optique` tinyint(1) NOT NULL DEFAULT '0',
  `maternite` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `action` enum('INSERT','UPDATE','DELETE','LOGIN','LOGOUT','OTHER') NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `date_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conge`
--

CREATE TABLE `conge` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type_conge_id` int UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_jours` int UNSIGNED NOT NULL,
  `motif` text,
  `statut` enum('en_attente','approuve','refuse','annule') NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conge_validation`
--

CREATE TABLE `conge_validation` (
  `id` int UNSIGNED NOT NULL,
  `conge_id` int UNSIGNED NOT NULL,
  `validateur_id` int UNSIGNED DEFAULT NULL,
  `decision` enum('approuve','refuse') NOT NULL,
  `commentaire` text,
  `decided_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `constat_document`
--

CREATE TABLE `constat_document` (
  `id` int UNSIGNED NOT NULL,
  `demande_id` int UNSIGNED NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `type_fichier` enum('photo','rapport','constat_amiable','autre') NOT NULL DEFAULT 'autre',
  `nom_original` varchar(255) DEFAULT NULL,
  `taille_ko` int UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `constat_history`
--

CREATE TABLE `constat_history` (
  `id` int UNSIGNED NOT NULL,
  `demande_id` int UNSIGNED NOT NULL,
  `old_statut` enum('soumis','en_cours','accepte','refuse','clos') DEFAULT NULL,
  `new_statut` enum('soumis','en_cours','accepte','refuse','clos') NOT NULL,
  `changed_by` int UNSIGNED DEFAULT NULL,
  `commentaire` text,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demande_constat`
--

CREATE TABLE `demande_constat` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `assurance_auto_id` int UNSIGNED NOT NULL,
  `description` text,
  `date_accident` date NOT NULL,
  `lieu_accident` varchar(255) DEFAULT NULL,
  `statut` enum('soumis','en_cours','accepte','refuse','clos') NOT NULL DEFAULT 'soumis',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inscription`
--

CREATE TABLE `inscription` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `offre_id` int UNSIGNED NOT NULL,
  `date_souscription` date NOT NULL,
  `date_expiration` date DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` enum('carte','virement','cheque','especes') DEFAULT NULL,
  `montant_paye` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inscription_history`
--

CREATE TABLE `inscription_history` (
  `id` int UNSIGNED NOT NULL,
  `inscription_id` int UNSIGNED NOT NULL,
  `old_status` enum('pending','paid','failed','refunded') DEFAULT NULL,
  `new_status` enum('pending','paid','failed','refunded') NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `titre` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type_notif` enum('info','alerte','paiement','constat','conge','system') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offre`
--

CREATE TABLE `offre` (
  `id` int UNSIGNED NOT NULL,
  `type_assurance_id` int UNSIGNED NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text,
  `prix_mensuel` decimal(10,2) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('active','inactive','archived') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `statut` enum('actif','masque','supprime') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tag_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `titre`, `contenu`, `is_pinned`, `statut`, `created_at`, `updated_at`, `tag_id`) VALUES
(1, 1, 'Bonjour', 'test', 0, 'supprime', '2026-04-21 08:39:44', '2026-04-28 09:36:21', NULL),
(2, 1, 'Bienvenue', 'Bienvenue dans notre forum!!', 0, 'actif', '2026-04-21 10:18:54', '2026-04-28 09:36:34', 2),
(3, 1, 'Règles du forum', '- Soyez respectueux\r\n- Pas de hors sujets', 0, 'actif', '2026-04-21 10:23:12', '2026-04-28 09:18:09', 1),
(4, 1, 'Probléme', 'qdfqdsfqdfqdf', 1, 'actif', '2026-04-28 09:18:53', '2026-04-28 09:40:54', 3);

-- --------------------------------------------------------

--
-- Table structure for table `reaction`
--

CREATE TABLE `reaction` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `post_id` int UNSIGNED DEFAULT NULL,
  `reply_id` int UNSIGNED DEFAULT NULL,
  `type_reaction` enum('like','dislike') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reaction`
--

INSERT INTO `reaction` (`id`, `user_id`, `post_id`, `reply_id`, `type_reaction`, `created_at`) VALUES
(1, 1, 1, NULL, 'like', '2026-04-21 08:39:52'),
(2, 1, 2, NULL, 'dislike', '2026-04-21 10:18:55'),
(3, 1, NULL, 1, 'like', '2026-04-21 10:24:09'),
(5, 1, 4, NULL, 'dislike', '2026-04-28 09:20:02'),
(6, 1, 3, NULL, 'dislike', '2026-04-28 09:20:07'),
(7, 1, NULL, 2, 'dislike', '2026-04-28 09:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `reply`
--

CREATE TABLE `reply` (
  `id` int UNSIGNED NOT NULL,
  `post_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `parent_reply_id` int UNSIGNED DEFAULT NULL,
  `contenu` text NOT NULL,
  `statut` enum('actif','masque','supprime') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reply`
--

INSERT INTO `reply` (`id`, `post_id`, `user_id`, `parent_reply_id`, `contenu`, `statut`, `created_at`, `updated_at`) VALUES
(1, 3, 1, NULL, 'Hi', 'actif', '2026-04-21 10:24:07', '2026-04-21 10:24:07'),
(2, 4, 1, NULL, 'dfdgfrgf', 'actif', '2026-04-28 09:41:38', '2026-04-28 09:41:38'),
(3, 4, 1, 2, 'cvfbfb', 'actif', '2026-04-28 09:41:43', '2026-04-28 09:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `reponse_constat`
--

CREATE TABLE `reponse_constat` (
  `id` int UNSIGNED NOT NULL,
  `demande_id` int UNSIGNED NOT NULL,
  `type_reponse_id` int UNSIGNED NOT NULL,
  `agent_id` int UNSIGNED DEFAULT NULL,
  `contenu` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int UNSIGNED NOT NULL,
  `reporter_id` int UNSIGNED NOT NULL,
  `post_id` int UNSIGNED DEFAULT NULL,
  `reply_id` int UNSIGNED DEFAULT NULL,
  `raison` text NOT NULL,
  `statut` enum('en_attente','traite','rejete') NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Full system access', '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(2, 'agent', 'Insurance agent — manages constats and offers', '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(3, 'client', 'Registered customer', '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(4, 'manager', 'HR manager — approves congés', '2026-04-21 08:34:50', '2026-04-21 08:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `color`) VALUES
(1, 'Question', '#561029'),
(2, 'Thread', '#f5ec00'),
(3, 'Réclamation', '#4d22b3'),
(4, 'Problème', '#0056d6'),
(5, 'test', '#00ffe1');

-- --------------------------------------------------------

--
-- Table structure for table `type_assurance`
--

CREATE TABLE `type_assurance` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `type_assurance`
--

INSERT INTO `type_assurance` (`id`, `nom`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Auto', 'Vehicle insurance covering accidents, theft, and liability', 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(2, 'Santé', 'Health insurance covering medical expenses', 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(3, 'Habitation', 'Home insurance covering property and contents', 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `type_conge`
--

CREATE TABLE `type_conge` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `jours_max_par_an` int UNSIGNED DEFAULT NULL,
  `justificatif_requis` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `type_conge`
--

INSERT INTO `type_conge` (`id`, `nom`, `jours_max_par_an`, `justificatif_requis`, `created_at`, `updated_at`) VALUES
(1, 'Congé annuel', 30, 0, '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(2, 'Congé maladie', 15, 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(3, 'Congé exceptionnel', 5, 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50'),
(4, 'Congé maternité', 98, 1, '2026-04-21 08:34:50', '2026-04-21 08:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `type_reponse`
--

CREATE TABLE `type_reponse` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `type_reponse`
--

INSERT INTO `type_reponse` (`id`, `nom`, `description`, `created_at`) VALUES
(1, 'Accusé de réception', 'Confirmation that the claim was received', '2026-04-21 08:34:50'),
(2, 'Demande de documents', 'Request for additional supporting documents', '2026-04-21 08:34:50'),
(3, 'Décision finale', 'Final acceptance or refusal of the claim', '2026-04-21 08:34:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','blocked','pending') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password_hash`, `phone`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Youssef', 'Jallouli', 'youssefjall@icloud.com', '$2a$12$H/wxchxZJ6MLCqfbK2eYXOyZY2rOCE0nNuN1g05oAFQD16Kt/Z3RO', NULL, NULL, 'active', '2026-04-21 08:38:18', '2026-04-28 08:36:55');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2026-04-21 08:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `user_status_history`
--

CREATE TABLE `user_status_history` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `old_status` enum('active','blocked','pending') DEFAULT NULL,
  `new_status` enum('active','blocked','pending') NOT NULL,
  `changed_by` int UNSIGNED DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assurance_auto`
--
ALTER TABLE `assurance_auto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offre_id` (`offre_id`),
  ADD UNIQUE KEY `immatriculation` (`immatriculation`),
  ADD KEY `idx_aa_offre_id` (`offre_id`);

--
-- Indexes for table `assurance_habitation`
--
ALTER TABLE `assurance_habitation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offre_id` (`offre_id`),
  ADD KEY `idx_ah_offre_id` (`offre_id`);

--
-- Indexes for table `assurance_sante`
--
ALTER TABLE `assurance_sante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offre_id` (`offre_id`),
  ADD KEY `idx_as_offre_id` (`offre_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_al_user_id` (`user_id`),
  ADD KEY `idx_al_table_name` (`table_name`),
  ADD KEY `idx_al_action` (`action`),
  ADD KEY `idx_al_date` (`date_action`);

--
-- Indexes for table `conge`
--
ALTER TABLE `conge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conge_user_id` (`user_id`),
  ADD KEY `idx_conge_type_conge_id` (`type_conge_id`),
  ADD KEY `idx_conge_statut` (`statut`);

--
-- Indexes for table `conge_validation`
--
ALTER TABLE `conge_validation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cv_conge_id` (`conge_id`),
  ADD KEY `idx_cv_validateur_id` (`validateur_id`);

--
-- Indexes for table `constat_document`
--
ALTER TABLE `constat_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cd_demande_id` (`demande_id`);

--
-- Indexes for table `constat_history`
--
ALTER TABLE `constat_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ch_demande_id` (`demande_id`),
  ADD KEY `idx_ch_changed_by` (`changed_by`);

--
-- Indexes for table `demande_constat`
--
ALTER TABLE `demande_constat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_dc_user_id` (`user_id`),
  ADD KEY `idx_dc_auto_id` (`assurance_auto_id`),
  ADD KEY `idx_dc_statut` (`statut`);

--
-- Indexes for table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_insc_user_id` (`user_id`),
  ADD KEY `idx_insc_offre_id` (`offre_id`);

--
-- Indexes for table `inscription_history`
--
ALTER TABLE `inscription_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ih_inscription_id` (`inscription_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user_id` (`user_id`),
  ADD KEY `idx_notif_is_read` (`is_read`);

--
-- Indexes for table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_offre_type` (`type_assurance_id`),
  ADD KEY `idx_offre_statut` (`statut`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_user_id` (`user_id`),
  ADD KEY `idx_post_statut` (`statut`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `reaction`
--
ALTER TABLE `reaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reaction_user_post` (`user_id`,`post_id`),
  ADD UNIQUE KEY `uq_reaction_user_reply` (`user_id`,`reply_id`),
  ADD KEY `idx_reaction_post_id` (`post_id`),
  ADD KEY `idx_reaction_reply_id` (`reply_id`);

--
-- Indexes for table `reply`
--
ALTER TABLE `reply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reply_post_id` (`post_id`),
  ADD KEY `idx_reply_user_id` (`user_id`),
  ADD KEY `idx_reply_parent_reply_id` (`parent_reply_id`);

--
-- Indexes for table `reponse_constat`
--
ALTER TABLE `reponse_constat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rc_demande_id` (`demande_id`),
  ADD KEY `idx_rc_type_reponse_id` (`type_reponse_id`),
  ADD KEY `idx_rc_agent_id` (`agent_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_reporter_id` (`reporter_id`),
  ADD KEY `idx_report_post_id` (`post_id`),
  ADD KEY `idx_report_reply_id` (`reply_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `type_assurance`
--
ALTER TABLE `type_assurance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `type_conge`
--
ALTER TABLE `type_conge`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `type_reponse`
--
ALTER TABLE `type_reponse`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_status` (`status`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `idx_ur_role_id` (`role_id`);

--
-- Indexes for table `user_status_history`
--
ALTER TABLE `user_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ush_user_id` (`user_id`),
  ADD KEY `idx_ush_changed_by` (`changed_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assurance_auto`
--
ALTER TABLE `assurance_auto`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assurance_habitation`
--
ALTER TABLE `assurance_habitation`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assurance_sante`
--
ALTER TABLE `assurance_sante`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conge`
--
ALTER TABLE `conge`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conge_validation`
--
ALTER TABLE `conge_validation`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `constat_document`
--
ALTER TABLE `constat_document`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `constat_history`
--
ALTER TABLE `constat_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `demande_constat`
--
ALTER TABLE `demande_constat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inscription_history`
--
ALTER TABLE `inscription_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reaction`
--
ALTER TABLE `reaction`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reply`
--
ALTER TABLE `reply`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reponse_constat`
--
ALTER TABLE `reponse_constat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `type_assurance`
--
ALTER TABLE `type_assurance`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `type_conge`
--
ALTER TABLE `type_conge`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `type_reponse`
--
ALTER TABLE `type_reponse`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_status_history`
--
ALTER TABLE `user_status_history`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assurance_auto`
--
ALTER TABLE `assurance_auto`
  ADD CONSTRAINT `fk_aa_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assurance_habitation`
--
ALTER TABLE `assurance_habitation`
  ADD CONSTRAINT `fk_ah_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assurance_sante`
--
ALTER TABLE `assurance_sante`
  ADD CONSTRAINT `fk_as_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `fk_conge_type_conge` FOREIGN KEY (`type_conge_id`) REFERENCES `type_conge` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_conge_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conge_validation`
--
ALTER TABLE `conge_validation`
  ADD CONSTRAINT `fk_cv_conge` FOREIGN KEY (`conge_id`) REFERENCES `conge` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cv_validateur` FOREIGN KEY (`validateur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `constat_document`
--
ALTER TABLE `constat_document`
  ADD CONSTRAINT `fk_cd_demande` FOREIGN KEY (`demande_id`) REFERENCES `demande_constat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `constat_history`
--
ALTER TABLE `constat_history`
  ADD CONSTRAINT `fk_ch_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ch_demande` FOREIGN KEY (`demande_id`) REFERENCES `demande_constat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `demande_constat`
--
ALTER TABLE `demande_constat`
  ADD CONSTRAINT `fk_dc_auto` FOREIGN KEY (`assurance_auto_id`) REFERENCES `assurance_auto` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `fk_insc_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_insc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `inscription_history`
--
ALTER TABLE `inscription_history`
  ADD CONSTRAINT `fk_ih_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscription` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `fk_offre_type` FOREIGN KEY (`type_assurance_id`) REFERENCES `type_assurance` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_post_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- Constraints for table `reaction`
--
ALTER TABLE `reaction`
  ADD CONSTRAINT `fk_reaction_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reaction_reply` FOREIGN KEY (`reply_id`) REFERENCES `reply` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reaction_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reply`
--
ALTER TABLE `reply`
  ADD CONSTRAINT `fk_reply_parent_reply` FOREIGN KEY (`parent_reply_id`) REFERENCES `reply` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reply_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reply_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reponse_constat`
--
ALTER TABLE `reponse_constat`
  ADD CONSTRAINT `fk_rc_agent` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rc_demande` FOREIGN KEY (`demande_id`) REFERENCES `demande_constat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rc_type_reponse` FOREIGN KEY (`type_reponse_id`) REFERENCES `type_reponse` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_report_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_report_reply` FOREIGN KEY (`reply_id`) REFERENCES `reply` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_report_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_status_history`
--
ALTER TABLE `user_status_history`
  ADD CONSTRAINT `fk_ush_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ush_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
