-- FaviconHub - Base de données


CREATE DATABASE IF NOT EXISTS `bdd_projet_web`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `bdd_projet_web`;

--Table utilisateurs
DROP TABLE IF EXISTS `t_favori_fav`;
DROP TABLE IF EXISTS `t_utilisateur_uti`;

CREATE TABLE `t_utilisateur_uti` (
    `uti_id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `uti_pseudo`           VARCHAR(255)     NOT NULL,
    `uti_email`            VARCHAR(255)     NOT NULL,
    `uti_motdepasse`       VARBINARY(255)   NOT NULL,
    `uti_compte_active`    TINYINT(1)       NOT NULL DEFAULT 1,
    `uti_code_activation`  CHAR(5)          DEFAULT NULL,
    `uti_date_inscription` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`uti_id`),
    UNIQUE KEY `uq_pseudo` (`uti_pseudo`),
    UNIQUE KEY `uq_email`  (`uti_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--Table favoris
CREATE TABLE `t_favori_fav` (
    `fav_id`        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `fav_uti_id`    INT UNSIGNED  NOT NULL,
    `fav_domaine`   VARCHAR(253)  NOT NULL,
    `fav_date_ajout` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`fav_id`),
    UNIQUE KEY `uq_favori` (`fav_uti_id`, `fav_domaine`),
    CONSTRAINT `fk_fav_uti`
        FOREIGN KEY (`fav_uti_id`)
        REFERENCES `t_utilisateur_uti` (`uti_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--Données de test
-- Mot de passe "Test1234!" (haché avec bcrypt, password_hash PHP)
INSERT INTO `t_utilisateur_uti`
    (`uti_pseudo`, `uti_email`, `uti_motdepasse`, `uti_compte_active`)
VALUES
    (
        'sasha',
        'sasha@exemple.be',
        '$2y$12$8K1p/a0dR6DdZ1JFqnqq.eGdoibM4KK1O2xFPTKMOvkNifgYqRXAO',
        1
    ),
    (
        'demo',
        'demo@exemple.be',
        '$2y$12$8K1p/a0dR6DdZ1JFqnqq.eGdoibM4KK1O2xFPTKMOvkNifgYqRXAO',
        1
    );

-- Favoris de test pour l'utilisateur "sasha"
INSERT INTO `t_favori_fav` (`fav_uti_id`, `fav_domaine`) VALUES
    (1, 'github.com'),
    (1, 'stackoverflow.com'),
    (1, 'mdn.dev'),
    (1, 'php.net'),
    (1, 'mysql.com');
