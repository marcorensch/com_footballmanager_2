-- implementing field for game program
ALTER TABLE `#__footballmanager_games`
    ADD COLUMN `game_program_link` VARCHAR(400) DEFAULT NULL AFTER `tickets_link`