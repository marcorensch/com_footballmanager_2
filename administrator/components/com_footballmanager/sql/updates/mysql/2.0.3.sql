ALTER TABLE `#__footballmanager_cheerleaders`
    ADD COLUMN `since` int(11) DEFAULT NULL AFTER `linked_team_id`,
    CHANGE COLUMN `linked_team_id` `team_id` int(11) DEFAULT NULL;

ALTER TABLE `#__footballmanager_officials`
    ADD COLUMN `since` int(11) DEFAULT NULL AFTER `linked_team_id`,
    CHANGE COLUMN `linked_team_id` `team_id` int(11) DEFAULT NULL;