/* Remove Foreign Key Constraint for Player - Country */

ALTER TABLE `#__footballmanager_players`
    DROP FOREIGN KEY `fk_players_country`;

ALTER TABLE `#__footballmanager_officials`
    DROP FOREIGN KEY `fk_officials_country`;

ALTER TABLE `#__footballmanager_coaches`
    DROP FOREIGN KEY`fk_coaches_country`;

ALTER TABLE `#__footballmanager_cheerleaders`
    DROP FOREIGN KEY `fk_cheerleaders_country`;

/* Add a new normalized Table for Player / Official / Staff / Cheerleaders Country Link */
CREATE TABLE IF NOT EXISTS `#__footballmanager_players_countries`
(
    `id`         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `player_id`  INT(11) NOT NULL,
    `country_id` INT(11) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 1,
    `created`    DATETIME   DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_player_country` (`player_id`, `country_id`),
    CONSTRAINT `fk_players_countries_player` FOREIGN KEY (`player_id`) REFERENCES `#__footballmanager_players` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_players_countries_country` FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_coaches_countries`
(
    `id`         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `coach_id`  INT(11) NOT NULL,
    `country_id` INT(11) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 1,
    `created`    DATETIME   DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_coach_country` (`coach_id`, `country_id`),
    CONSTRAINT `fk_coaches_countries_coach` FOREIGN KEY (`coach_id`) REFERENCES `#__footballmanager_coaches` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_coaches_countries_country` FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_officials_countries`
(
    `id`         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `official_id`  INT(11) NOT NULL,
    `country_id` INT(11) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 1,
    `created`    DATETIME   DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_official_country` (`official_id`, `country_id`),
    CONSTRAINT `fk_officials_countries_official` FOREIGN KEY (`official_id`) REFERENCES `#__footballmanager_officials` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_officials_countries_country` FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_cheerleaders_countries`
(
    `id`         INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `cheerleader_id`  INT(11) NOT NULL,
    `country_id` INT(11) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 1,
    `created`    DATETIME   DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_official_country` (`cheerleader_id`, `country_id`),
    CONSTRAINT `fk_cheerleaders_countries_cheerleader` FOREIGN KEY (`cheerleader_id`) REFERENCES `#__footballmanager_cheerleaders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cheerleaders_countries_country` FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (`id`) ON DELETE CASCADE
);


/* Migration: Copy Data from Player - Country Link Table */
INSERT INTO `#__footballmanager_players_countries` (player_id, country_id, is_primary, created)
SELECT
    p.id,
    p.country_id,
    1 AS is_primary,
    NOW() AS created
FROM `#__footballmanager_players` p
WHERE p.country_id IS NOT NULL;

INSERT INTO `#__footballmanager_officials_countries` (official_id, country_id, is_primary, created)
SELECT
    p.id,
    p.country_id,
    1 AS is_primary,
    NOW() AS created
FROM `#__footballmanager_officials` p
WHERE p.country_id IS NOT NULL;

INSERT INTO `#__footballmanager_coaches_countries` (coach_id, country_id, is_primary, created)
SELECT
    p.id,
    p.country_id,
    1 AS is_primary,
    NOW() AS created
FROM `#__footballmanager_coaches` p
WHERE p.country_id IS NOT NULL;

INSERT INTO `#__footballmanager_cheerleaders_countries` (cheerleader_id, country_id, is_primary, created)
SELECT
    p.id,
    p.country_id,
    1 AS is_primary,
    NOW() AS created
FROM `#__footballmanager_cheerleaders` p
WHERE p.country_id IS NOT NULL;