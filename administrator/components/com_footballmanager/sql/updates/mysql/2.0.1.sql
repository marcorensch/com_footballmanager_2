CREATE TABLE IF NOT EXISTS `#__footballmanager_countries`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `title`       varchar(255) NOT NULL,
    `alias`       varchar(255) NOT NULL,
    `iso`         varchar(2)            DEFAULT NULL,
    `iso3`        varchar(3)            DEFAULT NULL,
    `numcode`     smallint(6)           DEFAULT NULL,
    `params`      text                  DEFAULT NULL,
    `state`       tinyint(3)   NOT NULL DEFAULT 0,
    `published`   tinyint(1)   NOT NULL DEFAULT 0,
    `created_at`  datetime              DEFAULT NULL,
    `created_by`  int(11)               DEFAULT NULL,
    `modified_at` datetime              DEFAULT NOW(),
    `modified_by` int(11)               DEFAULT NULL,
    `version`     int(11)               DEFAULT 0,
    `catid`       int(11)      NOT NULL DEFAULT 0,
    `language`    char(7)      NOT NULL DEFAULT '',
    `ordering`    int(11)               DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`catid`)
);

ALTER TABLE `#__footballmanager_players`
    ADD COLUMN `country_id` int(11) DEFAULT NULL AFTER `sponsors`,
    ADD CONSTRAINT fk_players_country FOREIGN KEY (`country_id`) REFERENCES `#__footballmanager_countries` (id) ON DELETE SET NULL ON UPDATE CASCADE;
