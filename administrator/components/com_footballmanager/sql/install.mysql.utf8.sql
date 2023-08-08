CREATE TABLE IF NOT EXISTS `#__footballmanager_locations`
(
    `id`           int(11)          NOT NULL AUTO_INCREMENT,
    `title`        varchar(255)     NOT NULL,
    `alias`        varchar(255)     NOT NULL,
    `image`        varchar(255)              DEFAULT NULL,
    `description`  text                      DEFAULT NULL,
    `street`       varchar(255)              DEFAULT NULL,
    `city`         varchar(255)              DEFAULT NULL,
    `zip`          varchar(20)               DEFAULT NULL,
    `coordinates`  varchar(50)               DEFAULT NULL,
    `phone`        varchar(50)               DEFAULT NULL,
    `email`        varchar(100)              DEFAULT NULL,
    `website`      varchar(255)              DEFAULT NULL,
    `params`       text,
    `state`        tinyint(3)       NOT NULL DEFAULT 0,
    `published`    tinyint(1)       NOT NULL DEFAULT 0,
    `publish_up`   datetime,
    `publish_down` datetime,
    `language`     char(7)          NOT NULL DEFAULT '',
    `created_by`   int(11)                   DEFAULT NULL,
    `created_at`   datetime                  DEFAULT NULL,
    `modified_by`  int(11)                   DEFAULT NULL,
    `modified_at`  datetime                  DEFAULT NOW(),
    `version`      int(11)                   DEFAULT 0,
    `hits`         int(11)                   DEFAULT 0,
    `access`       int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`     int(11)                   DEFAULT 0,
    `catid`        int(11)          NOT NULL DEFAULT 0,
    `sponsors`     TEXT                      DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `idx_category` (`catid`),
    CONSTRAINT `unique_columns` UNIQUE (`alias`),
    CONSTRAINT `fk_locations_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_locations_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_sponsors`
(
    `id`           int(11)          NOT NULL AUTO_INCREMENT,
    `title`        varchar(255)     NOT NULL,
    `alias`        varchar(255)     NOT NULL,
    `logo`         varchar(255)              DEFAULT NULL,
    `image`        varchar(255)              DEFAULT NULL,
    `description`  text                      DEFAULT NULL,
    `street`       varchar(255)              DEFAULT NULL,
    `city`         varchar(255)              DEFAULT NULL,
    `zip`          varchar(20)               DEFAULT NULL,
    `website`      varchar(255)              DEFAULT NULL,
    `params`       text,
    `state`        tinyint(3)       NOT NULL DEFAULT 0,
    `published`    tinyint(1)       NOT NULL DEFAULT 0,
    `publish_up`   datetime,
    `publish_down` datetime,
    `language`     char(7)          NOT NULL DEFAULT '',
    `created_at`   datetime                  DEFAULT NULL,
    `created_by`   int(11)                   DEFAULT NULL,
    `modified_at`  datetime                  DEFAULT NOW(),
    `modified_by`  int(11)                   DEFAULT NULL,
    `version`      int(11)                   DEFAULT 0,
    `hits`         int(11)                   DEFAULT 0,
    `access`       int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`     int(11)                   DEFAULT 0,
    `catid`        int(11)          NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    KEY `idx_category` (`catid`),
    CONSTRAINT `unique_columns` UNIQUE (`alias`),
    CONSTRAINT `fk_sponsors_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_sponsors_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_leagues`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `title`       varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `image`       varchar(255)              DEFAULT NULL,
    `pts_loose`   int(11)                   DEFAULT 0,
    `pts_draw`    int(11)                   DEFAULT 1,
    `pts_win`     int(11)                   DEFAULT 2,
    `params`      text,
    `state`       tinyint(3)       NOT NULL DEFAULT 0,
    `published`   tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`  datetime                  DEFAULT NULL,
    `created_by`  int(11)                   DEFAULT NULL,
    `modified_at` datetime                  DEFAULT NOW(),
    `modified_by` int(11)                   DEFAULT NULL,
    `version`     int(11)                   DEFAULT 0,
    `hits`        int(11)                   DEFAULT 0,
    `access`      int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`    int(11)                   DEFAULT 0,

    PRIMARY KEY (`id`),

    CONSTRAINT `unique_columns` UNIQUE (`alias`),
    CONSTRAINT `fk_leagues_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_leagues_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_seasons`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `title`       varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `params`      text,
    `state`       tinyint(3)       NOT NULL DEFAULT 0,
    `published`   tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`  datetime                  DEFAULT NULL,
    `created_by`  int(11)                   DEFAULT NULL,
    `modified_at` datetime                  DEFAULT NOW(),
    `modified_by` int(11)                   DEFAULT NULL,
    `version`     int(11)                   DEFAULT 0,
    `hits`        int(11)                   DEFAULT 0,
    `access`      int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`    int(11)                   DEFAULT 0,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_season_phases`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `title`       varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `params`      text,
    `state`       tinyint(3)       NOT NULL DEFAULT 0,
    `published`   tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`  datetime                  DEFAULT NULL,
    `created_by`  int(11)                   DEFAULT NULL,
    `modified_at` datetime                  DEFAULT NOW(),
    `modified_by` int(11)                   DEFAULT NULL,
    `version`     int(11)                   DEFAULT 0,
    `hits`        int(11)                   DEFAULT 0,
    `access`      int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`    int(11)                   DEFAULT 0,

    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_teams`
(
    `id`               int(11)          NOT NULL AUTO_INCREMENT,
    `title`            varchar(255)     NOT NULL,
    `alias`            varchar(255)     NOT NULL,
    `shortname`        varchar(100)     NOT NULL,
    `shortcode`        varchar(10)      NOT NULL,
    `introtext`        text                      DEFAULT NULL,
    `description`      text                      DEFAULT NULL,
    `sponsors`         text                      DEFAULT NULL,
    `year_established` datetime                  DEFAULT NULL,
    `logo`             varchar(255)              DEFAULT NULL,
    `image`            varchar(255)              DEFAULT NULL,
    `color`            varchar(255)              DEFAULT NULL,
    `my_team`          tinyint(1)                DEFAULT 0,
    `location_id`      int(11)                   DEFAULT NULL,
    `street`           varchar(255)              DEFAULT NULL,
    `city`             varchar(255)              DEFAULT NULL,
    `zip`              varchar(20)               DEFAULT NULL,
    `website`          varchar(255)              DEFAULT NULL,
    `email`            varchar(255)              DEFAULT NULL,
    `phone`            varchar(100)              DEFAULT NULL,
    `params`           text,
    `state`            tinyint(3)       NOT NULL DEFAULT 0,
    `published`        tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`       datetime                  DEFAULT NULL,
    `created_by`       int(11)                   DEFAULT NULL,
    `modified_at`      datetime                  DEFAULT NOW(),
    `modified_by`      int(11)                   DEFAULT NULL,
    `version`          int(11)                   DEFAULT 0,
    `hits`             int(11)                   DEFAULT 0,
    `access`           int(10) unsigned NOT NULL DEFAULT 0,
    `ordering`         int(11)                   DEFAULT 0,
    `catid`            int(11)          NOT NULL DEFAULT 0,
    `language`         char(7)          NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    KEY `idx_category` (`catid`),
    CONSTRAINT `unique_columns` UNIQUE (`alias`),
    CONSTRAINT `fk_teams_location_id` FOREIGN KEY (`location_id`) REFERENCES `#__footballmanager_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_teams_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_teams_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_coaches`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(255)     NOT NULL,
    `lastname`    varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `about`       text                      DEFAULT NULL,
    `image`       varchar(255)              DEFAULT NULL,
    `params`      text,
    `state`       tinyint(3)       NOT NULL DEFAULT 0,
    `published`   tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`  datetime                  DEFAULT NULL,
    `created_by`  int(11)                   DEFAULT NULL,
    `modified_at` datetime                  DEFAULT NOW(),
    `modified_by` int(11)                   DEFAULT NULL,
    `version`     int(11)                   DEFAULT 0,
    `catid`       int(11)          NOT NULL DEFAULT 0,
    `hits`        int(11)                   DEFAULT 0,
    `access`      int(10) unsigned NOT NULL DEFAULT 0,
    `language`    char(7)          NOT NULL DEFAULT '',
    `ordering`    int(11)                   DEFAULT 0,

    PRIMARY KEY (`id`),
    KEY `idx_category` (`catid`),
    CONSTRAINT `fk_coaches_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_coaches_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_coaches_teams`
(
    `id`       int(11)      NOT NULL AUTO_INCREMENT,
    `team_id`  int(11)      NOT NULL,
    `coach_id` int(11)      NOT NULL,
    `photo`    varchar(255) NOT NULL,
    `position` int(11)      NOT NULL,
    `since`    datetime DEFAULT NULL,
    `until`    datetime DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `idx_team` (`team_id`),
    KEY `idx_coach` (`coach_id`),
    CONSTRAINT `fk_coaches_teams_team_id` FOREIGN KEY (`team_id`) REFERENCES `#__footballmanager_teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_coaches_teams_coach_id` FOREIGN KEY (`coach_id`) REFERENCES `#__footballmanager_coaches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_positions`
(
    `id`             int(11)          NOT NULL AUTO_INCREMENT,
    `title`          varchar(255)     NOT NULL,
    `alias`          varchar(255)     NOT NULL,
    `learnmore_link` varchar(255)              DEFAULT NULL,
    `description`    text                      DEFAULT NULL,
    `params`         text,
    `state`          tinyint(3)       NOT NULL DEFAULT 0,
    `published`      tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`     datetime                  DEFAULT NULL,
    `created_by`     int(11)                   DEFAULT NULL,
    `modified_at`    datetime                  DEFAULT NOW(),
    `modified_by`    int(11)                   DEFAULT NULL,
    `version`        int(11)                   DEFAULT 0,
    `access`         int(10) unsigned NOT NULL DEFAULT 0,
    `language`       char(7)          NOT NULL DEFAULT '',
    `ordering`       int(11)                   DEFAULT 0,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk_positions_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_positions_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);