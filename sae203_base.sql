-- =========================================================
-- Base de données E-LLUSION
-- À importer dans phpMyAdmin (MMI Agence ou OVH)
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------
-- Table `salle`
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `salle`;
CREATE TABLE `salle` (
  `id_salle`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom`          VARCHAR(100) NOT NULL,
  `capacite_max` INT UNSIGNED NOT NULL DEFAULT 12,
  PRIMARY KEY (`id_salle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `salle` (`nom`, `capacite_max`) VALUES
  ('Societ-e',          12),
  ('Horizon',           12),
  ("L'envers du Décor", 12),
  ('La pépinière',      12);

-- -----------------------------------------------------------
-- Table `creneau`
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `creneau`;
CREATE TABLE `creneau` (
  `id_creneau`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_salle`         INT UNSIGNED NOT NULL,
  `date`             DATE NOT NULL,
  `heure_debut`      TIME NOT NULL,
  `places_restantes` INT NOT NULL,
  PRIMARY KEY (`id_creneau`),
  FOREIGN KEY (`id_salle`) REFERENCES `salle`(`id_salle`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jeudi 18 juin 2026 — 10 créneaux × 4 salles = 40 créneaux
-- Les places_restantes sont initialisées à capacite_max (12)
INSERT INTO `creneau` (`id_salle`, `date`, `heure_debut`, `places_restantes`) VALUES
  -- Salle 1 : Societ-e
  (1, '2026-06-18', '15:00:00', 12),
  (1, '2026-06-18', '15:30:00', 12),
  (1, '2026-06-18', '16:00:00', 12),
  (1, '2026-06-18', '16:30:00', 12),
  (1, '2026-06-18', '17:00:00', 12),
  (1, '2026-06-18', '17:30:00', 12),
  (1, '2026-06-18', '18:00:00', 12),
  (1, '2026-06-18', '19:00:00', 12),
  (1, '2026-06-18', '19:30:00', 12),
  (1, '2026-06-18', '20:00:00', 12),
  -- Salle 2 : Horizon
  (2, '2026-06-18', '15:00:00', 12),
  (2, '2026-06-18', '15:30:00', 12),
  (2, '2026-06-18', '16:00:00', 12),
  (2, '2026-06-18', '16:30:00', 12),
  (2, '2026-06-18', '17:00:00', 12),
  (2, '2026-06-18', '17:30:00', 12),
  (2, '2026-06-18', '18:00:00', 12),
  (2, '2026-06-18', '19:00:00', 12),
  (2, '2026-06-18', '19:30:00', 12),
  (2, '2026-06-18', '20:00:00', 12),
  -- Salle 3 : L'envers du Décor
  (3, '2026-06-18', '15:00:00', 12),
  (3, '2026-06-18', '15:30:00', 12),
  (3, '2026-06-18', '16:00:00', 12),
  (3, '2026-06-18', '16:30:00', 12),
  (3, '2026-06-18', '17:00:00', 12),
  (3, '2026-06-18', '17:30:00', 12),
  (3, '2026-06-18', '18:00:00', 12),
  (3, '2026-06-18', '19:00:00', 12),
  (3, '2026-06-18', '19:30:00', 12),
  (3, '2026-06-18', '20:00:00', 12),
  -- Salle 4 : La pépinière
  (4, '2026-06-18', '15:00:00', 12),
  (4, '2026-06-18', '15:30:00', 12),
  (4, '2026-06-18', '16:00:00', 12),
  (4, '2026-06-18', '16:30:00', 12),
  (4, '2026-06-18', '17:00:00', 12),
  (4, '2026-06-18', '17:30:00', 12),
  (4, '2026-06-18', '18:00:00', 12),
  (4, '2026-06-18', '19:00:00', 12),
  (4, '2026-06-18', '19:30:00', 12),
  (4, '2026-06-18', '20:00:00', 12);

-- -----------------------------------------------------------
-- Table `categorie_visite`
-- IMPORTANT : vérifiez que ces libellés correspondent
-- exactement à ceux de votre BDD OVH !
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `categorie_visite`;
CREATE TABLE `categorie_visite` (
  `id_categorie` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle`      VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorie_visite` (`libelle`) VALUES
  ('Grand public'),
  ('Étudiant'),
  ('Enseignant'),
  ('Professionnel');

-- -----------------------------------------------------------
-- Table `inscription`
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `inscription`;
CREATE TABLE `inscription` (
  `id_inscription` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_creneau`     INT UNSIGNED NOT NULL,
  `id_categorie`   INT UNSIGNED NOT NULL,
  `nom`            VARCHAR(100) NOT NULL,
  `prenom`         VARCHAR(100) NOT NULL,
  `email`          VARCHAR(255) NOT NULL,
  `nb_personnes`   INT UNSIGNED NOT NULL DEFAULT 1,
  `buffet`         TINYINT(1) NOT NULL DEFAULT 0,
  `token`          VARCHAR(64) NOT NULL,
  `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inscription`),
  UNIQUE KEY `token` (`token`),
  FOREIGN KEY (`id_creneau`) REFERENCES `creneau`(`id_creneau`) ON DELETE RESTRICT,
  FOREIGN KEY (`id_categorie`) REFERENCES `categorie_visite`(`id_categorie`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------
-- Table `admin`
-- Le compte se crée via admin/creer-admin.php
-- -----------------------------------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id_admin`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login`         VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
