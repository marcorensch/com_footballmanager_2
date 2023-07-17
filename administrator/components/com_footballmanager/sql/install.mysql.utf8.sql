CREATE TABLE IF NOT EXISTS `#__footballmanager_locations`
(
    `id`           int(11)          NOT NULL AUTO_INCREMENT,
    `title`        varchar(255)     NOT NULL,
    `alias`        varchar(255)     NOT NULL,
    `image`        varchar(255)              DEFAULT NULL,
    `description`  text,
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
    `description`  text,
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

/* Auto Generate: GENERATED ALWAYS AS (lower(replace(name, ' ', '-'))) STORED, */