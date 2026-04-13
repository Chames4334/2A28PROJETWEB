-- =============================================================
--  INSURANCE MANAGEMENT SYSTEM — Full Database Schema
--  Compatible with: MySQL 8.0+ / XAMPP / phpMyAdmin
--  Encoding: UTF-8 | Engine: InnoDB | PDO-ready
-- =============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `insurance_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `insurance_db`;


-- =============================================================
--  MODULE 1 — USER MANAGEMENT
-- =============================================================

-- ── Roles (replaces ENUM role) ────────────────────────────────
CREATE TABLE `roles` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(50)      NOT NULL UNIQUE,
  `description` VARCHAR(255)     DEFAULT NULL,
  `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Users ─────────────────────────────────────────────────────
CREATE TABLE `users` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `nom`           VARCHAR(100)   NOT NULL,
  `prenom`        VARCHAR(100)   NOT NULL,
  `email`         VARCHAR(191)   NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)   NOT NULL,
  `phone`         VARCHAR(30)    DEFAULT NULL,
  `address`       VARCHAR(255)   DEFAULT NULL,
  `status`        ENUM('active','blocked','pending') NOT NULL DEFAULT 'active',
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_users_email`  (`email`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── User ↔ Roles (many-to-many) ───────────────────────────────
CREATE TABLE `user_roles` (
  `user_id`     INT UNSIGNED NOT NULL,
  `role_id`     INT UNSIGNED NOT NULL,
  `assigned_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  INDEX `idx_ur_role_id` (`role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)  ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── User status change history ────────────────────────────────
CREATE TABLE `user_status_history` (
  `id`           INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED   NOT NULL,
  `old_status`   ENUM('active','blocked','pending') DEFAULT NULL,
  `new_status`   ENUM('active','blocked','pending') NOT NULL,
  `changed_by`   INT UNSIGNED   DEFAULT NULL,
  `reason`       VARCHAR(255)   DEFAULT NULL,
  `changed_at`   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ush_user_id`   (`user_id`),
  INDEX `idx_ush_changed_by`(`changed_by`),
  CONSTRAINT `fk_ush_user`       FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_ush_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 2 — OFFRE / INSURANCE TYPES
-- =============================================================

-- ── Insurance types (replaces ENUM type_assurance) ───────────
CREATE TABLE `type_assurance` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(100)  NOT NULL UNIQUE,
  `description` TEXT          DEFAULT NULL,
  `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Offre ─────────────────────────────────────────────────────
CREATE TABLE `offre` (
  `id`                 INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `type_assurance_id`  INT UNSIGNED    NOT NULL,
  `titre`              VARCHAR(150)    NOT NULL,
  `description`        TEXT            DEFAULT NULL,
  `prix_mensuel`       DECIMAL(10,2)   NOT NULL,
  `date_debut`         DATE            NOT NULL,
  `date_fin`           DATE            DEFAULT NULL,
  `statut`             ENUM('active','inactive','archived') NOT NULL DEFAULT 'active',
  `created_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_offre_type`   (`type_assurance_id`),
  INDEX `idx_offre_statut` (`statut`),
  CONSTRAINT `fk_offre_type` FOREIGN KEY (`type_assurance_id`) REFERENCES `type_assurance`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Inscription (subscription) ────────────────────────────────
CREATE TABLE `inscription` (
  `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`           INT UNSIGNED    NOT NULL,
  `offre_id`          INT UNSIGNED    NOT NULL,
  `date_souscription` DATE            NOT NULL,
  `date_expiration`   DATE            DEFAULT NULL,
  `payment_status`    ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method`    ENUM('carte','virement','cheque','especes') DEFAULT NULL,
  `montant_paye`      DECIMAL(10,2)   DEFAULT NULL,
  `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_insc_user_id`  (`user_id`),
  INDEX `idx_insc_offre_id` (`offre_id`),
  CONSTRAINT `fk_insc_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_insc_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre`(`id`)  ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Inscription history ───────────────────────────────────────
CREATE TABLE `inscription_history` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inscription_id` INT UNSIGNED NOT NULL,
  `old_status`     ENUM('pending','paid','failed','refunded') DEFAULT NULL,
  `new_status`     ENUM('pending','paid','failed','refunded') NOT NULL,
  `notes`          VARCHAR(255) DEFAULT NULL,
  `changed_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ih_inscription_id` (`inscription_id`),
  CONSTRAINT `fk_ih_inscription` FOREIGN KEY (`inscription_id`) REFERENCES `inscription`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 3 — SPECIALIZATION TABLES (1:1 with offre)
-- =============================================================

-- ── Assurance Auto ────────────────────────────────────────────
CREATE TABLE `assurance_auto` (
  `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `offre_id`         INT UNSIGNED    NOT NULL UNIQUE,
  `marque`           VARCHAR(100)    NOT NULL,
  `modele`           VARCHAR(100)    NOT NULL,
  `annee`            YEAR            NOT NULL,
  `immatriculation`  VARCHAR(30)     NOT NULL UNIQUE,
  `puissance_cv`     INT UNSIGNED    DEFAULT NULL,
  `valeur_vehicule`  DECIMAL(12,2)   DEFAULT NULL,
  `type_carburant`   ENUM('essence','diesel','electrique','hybride','gpl') DEFAULT NULL,
  `created_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_aa_offre_id` (`offre_id`),
  CONSTRAINT `fk_aa_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Assurance Santé ───────────────────────────────────────────
CREATE TABLE `assurance_sante` (
  `id`                      INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `offre_id`                INT UNSIGNED  NOT NULL UNIQUE,
  `age_limit`               TINYINT UNSIGNED DEFAULT NULL,
  `type_couverture`         ENUM('individuelle','familiale','entreprise') NOT NULL,
  `plafond_remboursement`   DECIMAL(10,2)   DEFAULT NULL,
  `dentaire`                TINYINT(1)      NOT NULL DEFAULT 0,
  `optique`                 TINYINT(1)      NOT NULL DEFAULT 0,
  `maternite`               TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`              TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`              TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_as_offre_id` (`offre_id`),
  CONSTRAINT `fk_as_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Assurance Habitation ──────────────────────────────────────
CREATE TABLE `assurance_habitation` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `offre_id`      INT UNSIGNED   NOT NULL UNIQUE,
  `adresse`       VARCHAR(255)   NOT NULL,
  `localisation`  VARCHAR(150)   DEFAULT NULL,
  `surface_m2`    DECIMAL(8,2)   DEFAULT NULL,
  `type_logement` ENUM('appartement','maison','villa','studio','local_commercial') DEFAULT NULL,
  `valeur_bien`   DECIMAL(12,2)  DEFAULT NULL,
  `meuble`        TINYINT(1)     NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ah_offre_id` (`offre_id`),
  CONSTRAINT `fk_ah_offre` FOREIGN KEY (`offre_id`) REFERENCES `offre`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 4 — CONSTATS (AUTO only)
-- =============================================================

-- ── Type de réponse (replaces ENUM) ──────────────────────────
CREATE TABLE `type_reponse` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`         VARCHAR(100) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Demande constat ───────────────────────────────────────────
CREATE TABLE `demande_constat` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`            INT UNSIGNED NOT NULL,
  `assurance_auto_id`  INT UNSIGNED NOT NULL,
  `description`        TEXT         DEFAULT NULL,
  `date_accident`      DATE         NOT NULL,
  `lieu_accident`      VARCHAR(255) DEFAULT NULL,
  `statut`             ENUM('soumis','en_cours','accepte','refuse','clos') NOT NULL DEFAULT 'soumis',
  `created_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_dc_user_id`   (`user_id`),
  INDEX `idx_dc_auto_id`   (`assurance_auto_id`),
  INDEX `idx_dc_statut`    (`statut`),
  CONSTRAINT `fk_dc_user` FOREIGN KEY (`user_id`)           REFERENCES `users`(`id`)           ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_dc_auto` FOREIGN KEY (`assurance_auto_id`) REFERENCES `assurance_auto`(`id`)  ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Réponse constat (1:N per demande) ────────────────────────
CREATE TABLE `reponse_constat` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `demande_id`      INT UNSIGNED NOT NULL,
  `type_reponse_id` INT UNSIGNED NOT NULL,
  `agent_id`        INT UNSIGNED DEFAULT NULL,
  `contenu`         TEXT         NOT NULL,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_rc_demande_id`      (`demande_id`),
  INDEX `idx_rc_type_reponse_id` (`type_reponse_id`),
  INDEX `idx_rc_agent_id`        (`agent_id`),
  CONSTRAINT `fk_rc_demande`      FOREIGN KEY (`demande_id`)      REFERENCES `demande_constat`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_rc_type_reponse` FOREIGN KEY (`type_reponse_id`) REFERENCES `type_reponse`(`id`)    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_rc_agent`        FOREIGN KEY (`agent_id`)        REFERENCES `users`(`id`)           ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Constat documents (file uploads) ─────────────────────────
CREATE TABLE `constat_document` (
  `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `demande_id`    INT UNSIGNED  NOT NULL,
  `file_path`     VARCHAR(500)  NOT NULL,
  `type_fichier`  ENUM('photo','rapport','constat_amiable','autre') NOT NULL DEFAULT 'autre',
  `nom_original`  VARCHAR(255)  DEFAULT NULL,
  `taille_ko`     INT UNSIGNED  DEFAULT NULL,
  `uploaded_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_cd_demande_id` (`demande_id`),
  CONSTRAINT `fk_cd_demande` FOREIGN KEY (`demande_id`) REFERENCES `demande_constat`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Constat status history ────────────────────────────────────
CREATE TABLE `constat_history` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `demande_id`   INT UNSIGNED  NOT NULL,
  `old_statut`   ENUM('soumis','en_cours','accepte','refuse','clos') DEFAULT NULL,
  `new_statut`   ENUM('soumis','en_cours','accepte','refuse','clos') NOT NULL,
  `changed_by`   INT UNSIGNED  DEFAULT NULL,
  `commentaire`  TEXT          DEFAULT NULL,
  `changed_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_ch_demande_id`  (`demande_id`),
  INDEX `idx_ch_changed_by`  (`changed_by`),
  CONSTRAINT `fk_ch_demande`     FOREIGN KEY (`demande_id`) REFERENCES `demande_constat`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_ch_changed_by`  FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`)           ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 5 — CONGÉS
-- =============================================================

-- ── Type congé (replaces ENUM) ────────────────────────────────
CREATE TABLE `type_conge` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`                  VARCHAR(100) NOT NULL UNIQUE,
  `jours_max_par_an`     INT UNSIGNED DEFAULT NULL,
  `justificatif_requis`  TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Congé ─────────────────────────────────────────────────────
CREATE TABLE `conge` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED NOT NULL,
  `type_conge_id`  INT UNSIGNED NOT NULL,
  `date_debut`     DATE         NOT NULL,
  `date_fin`       DATE         NOT NULL,
  `nb_jours`       INT UNSIGNED NOT NULL,
  `motif`          TEXT         DEFAULT NULL,
  `statut`         ENUM('en_attente','approuve','refuse','annule') NOT NULL DEFAULT 'en_attente',
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_conge_user_id`       (`user_id`),
  INDEX `idx_conge_type_conge_id` (`type_conge_id`),
  INDEX `idx_conge_statut`        (`statut`),
  CONSTRAINT `fk_conge_user`       FOREIGN KEY (`user_id`)       REFERENCES `users`(`id`)      ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_conge_type_conge` FOREIGN KEY (`type_conge_id`) REFERENCES `type_conge`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Congé validation history ──────────────────────────────────
CREATE TABLE `conge_validation` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conge_id`      INT UNSIGNED NOT NULL,
  `validateur_id` INT UNSIGNED DEFAULT NULL,
  `decision`      ENUM('approuve','refuse') NOT NULL,
  `commentaire`   TEXT         DEFAULT NULL,
  `decided_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_cv_conge_id`      (`conge_id`),
  INDEX `idx_cv_validateur_id` (`validateur_id`),
  CONSTRAINT `fk_cv_conge`      FOREIGN KEY (`conge_id`)      REFERENCES `conge`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_cv_validateur` FOREIGN KEY (`validateur_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 6 — COMMUNITY
-- =============================================================

-- ── Post ─────────────────────────────────────────────────────
CREATE TABLE `post` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `titre`      VARCHAR(200) NOT NULL,
  `contenu`    TEXT         NOT NULL,
  `is_pinned`  TINYINT(1)   NOT NULL DEFAULT 0,
  `statut`     ENUM('actif','masque','supprime') NOT NULL DEFAULT 'actif',
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_post_user_id` (`user_id`),
  INDEX `idx_post_statut`  (`statut`),
  CONSTRAINT `fk_post_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Reply (hierarchical: reply to reply) ─────────────────────
CREATE TABLE `reply` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`         INT UNSIGNED NOT NULL,
  `user_id`         INT UNSIGNED NOT NULL,
  `parent_reply_id` INT UNSIGNED DEFAULT NULL,
  `contenu`         TEXT         NOT NULL,
  `statut`          ENUM('actif','masque','supprime') NOT NULL DEFAULT 'actif',
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_reply_post_id`         (`post_id`),
  INDEX `idx_reply_user_id`         (`user_id`),
  INDEX `idx_reply_parent_reply_id` (`parent_reply_id`),
  CONSTRAINT `fk_reply_post`         FOREIGN KEY (`post_id`)         REFERENCES `post`(`id`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_reply_user`         FOREIGN KEY (`user_id`)         REFERENCES `users`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_reply_parent_reply` FOREIGN KEY (`parent_reply_id`) REFERENCES `reply`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Reactions (like/dislike on post or reply) ─────────────────
CREATE TABLE `reaction` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`        INT UNSIGNED NOT NULL,
  `post_id`        INT UNSIGNED DEFAULT NULL,
  `reply_id`       INT UNSIGNED DEFAULT NULL,
  `type_reaction`  ENUM('like','dislike') NOT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reaction_user_post`  (`user_id`,`post_id`),
  UNIQUE KEY `uq_reaction_user_reply` (`user_id`,`reply_id`),
  INDEX `idx_reaction_post_id`  (`post_id`),
  INDEX `idx_reaction_reply_id` (`reply_id`),
  CONSTRAINT `fk_reaction_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_reaction_post`  FOREIGN KEY (`post_id`)  REFERENCES `post`(`id`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_reaction_reply` FOREIGN KEY (`reply_id`) REFERENCES `reply`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Report (flag inappropriate content) ──────────────────────
CREATE TABLE `report` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reporter_id` INT UNSIGNED NOT NULL,
  `post_id`     INT UNSIGNED DEFAULT NULL,
  `reply_id`    INT UNSIGNED DEFAULT NULL,
  `raison`      TEXT         NOT NULL,
  `statut`      ENUM('en_attente','traite','rejete') NOT NULL DEFAULT 'en_attente',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` TIMESTAMP    DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_report_reporter_id` (`reporter_id`),
  INDEX `idx_report_post_id`     (`post_id`),
  INDEX `idx_report_reply_id`    (`reply_id`),
  CONSTRAINT `fk_report_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_report_post`     FOREIGN KEY (`post_id`)     REFERENCES `post`(`id`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `fk_report_reply`    FOREIGN KEY (`reply_id`)    REFERENCES `reply`(`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  MODULE 7 — AUDIT & NOTIFICATIONS
-- =============================================================

-- ── Audit log ─────────────────────────────────────────────────
CREATE TABLE `audit_log` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED     DEFAULT NULL,
  `action`      ENUM('INSERT','UPDATE','DELETE','LOGIN','LOGOUT','OTHER') NOT NULL,
  `table_name`  VARCHAR(100)     NOT NULL,
  `record_id`   INT UNSIGNED     DEFAULT NULL,
  `old_values`  JSON             DEFAULT NULL,
  `new_values`  JSON             DEFAULT NULL,
  `ip_address`  VARCHAR(45)      DEFAULT NULL,
  `date_action` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_al_user_id`    (`user_id`),
  INDEX `idx_al_table_name` (`table_name`),
  INDEX `idx_al_action`     (`action`),
  INDEX `idx_al_date`       (`date_action`),
  CONSTRAINT `fk_al_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Notifications ─────────────────────────────────────────────
CREATE TABLE `notification` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `titre`       VARCHAR(200) NOT NULL,
  `message`     TEXT         NOT NULL,
  `type_notif`  ENUM('info','alerte','paiement','constat','conge','system') NOT NULL DEFAULT 'info',
  `is_read`     TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at`     TIMESTAMP    DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_notif_user_id` (`user_id`),
  INDEX `idx_notif_is_read` (`is_read`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =============================================================
--  SEED DATA — Default roles and type_assurance
-- =============================================================

INSERT INTO `roles` (`nom`, `description`) VALUES
  ('admin',    'Full system access'),
  ('agent',    'Insurance agent — manages constats and offers'),
  ('client',   'Registered customer'),
  ('manager',  'HR manager — approves congés');

INSERT INTO `type_assurance` (`nom`, `description`) VALUES
  ('Auto',        'Vehicle insurance covering accidents, theft, and liability'),
  ('Santé',       'Health insurance covering medical expenses'),
  ('Habitation',  'Home insurance covering property and contents');

INSERT INTO `type_conge` (`nom`, `jours_max_par_an`, `justificatif_requis`) VALUES
  ('Congé annuel',      30, 0),
  ('Congé maladie',     15, 1),
  ('Congé exceptionnel', 5, 1),
  ('Congé maternité',   98, 1);

INSERT INTO `type_reponse` (`nom`, `description`) VALUES
  ('Accusé de réception',  'Confirmation that the claim was received'),
  ('Demande de documents', 'Request for additional supporting documents'),
  ('Décision finale',      'Final acceptance or refusal of the claim');

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
--  END OF FILE
--  Tables: 24 | Relationships: 30+ | Indexes: 50+
--  Modules: Users · Offres · Constats · Congés · Community · Audit
-- =============================================================
