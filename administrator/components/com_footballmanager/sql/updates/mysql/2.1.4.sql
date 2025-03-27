/* Adding inverted_logo and secondary_color */
ALTER TABLE `#__footballmanager_teams`
    ADD COLUMN `inverted_logo` VARCHAR(255) DEFAULT NULL AFTER `logo`,
    ADD COLUMN `secondary_color` VARCHAR(50) DEFAULT NULL AFTER `color`;
