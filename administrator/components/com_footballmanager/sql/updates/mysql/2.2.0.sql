/* Adding field for match ball sponsor */

ALTER TABLE `#__footballmanager_games`
    ADD COLUMN `matchball_sponsor_id` INT(11) DEFAULT NULL AFTER `sponsors`,
    ADD CONSTRAINT fk_matchball_sponsor_id FOREIGN KEY (`matchball_sponsor_id`) REFERENCES `#__footballmanager_sponsors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
