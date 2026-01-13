ALTER TABLE `#__footballmanager_players_countries`
    DROP COLUMN `is_primary`,
    ADD COLUMN `ordering` INT(11) DEFAULT 0;

ALTER TABLE `#__footballmanager_coaches_countries`
    DROP COLUMN `is_primary`,
    ADD COLUMN `ordering` INT(11) DEFAULT 0;

ALTER TABLE `#__footballmanager_officials_countries`
    DROP COLUMN `is_primary`,
    ADD COLUMN `ordering` INT(11) DEFAULT 0;

ALTER TABLE `#__footballmanager_cheerleaders_countries`
    DROP COLUMN `is_primary`,
    ADD COLUMN `ordering` INT(11) DEFAULT 0;
