ALTER TABLE `#__footballmanager_coaches`
    ADD COLUMN `country_id` int(11) DEFAULT NULL AFTER `image`,
    ADD CONSTRAINT fk_coaches_country FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `#__footballmanager_cheerleaders`
    ADD COLUMN `country_id` int(11) DEFAULT NULL AFTER `image`,
    ADD CONSTRAINT fk_cheerleaders_country FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (id) ON DELETE SET NULL ON UPDATE CASCADE;
