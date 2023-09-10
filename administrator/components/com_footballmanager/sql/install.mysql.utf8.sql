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
    `params`       text                      DEFAULT NULL,
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
    `params`       text                      DEFAULT NULL,
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
    `params`      text                      DEFAULT NULL,
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
    `params`      text                      DEFAULT NULL,
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
    `params`      text                      DEFAULT NULL,
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
    `params`           text                      DEFAULT NULL,
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
    `params`      text                      DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `#__footballmanager_referees`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(255)     NOT NULL,
    `lastname`    varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `about`       text                      DEFAULT NULL,
    `image`       varchar(255)              DEFAULT NULL,
    `params`      text                      DEFAULT NULL,
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
    CONSTRAINT `fk_referees_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_referees_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_coaches_teams`
(
    `id`          int(11)      NOT NULL AUTO_INCREMENT,
    `team_id`     int(11)               DEFAULT NULL,
    `coach_id`    int(11)      NOT NULL,
    `position_id` int(11)               DEFAULT NULL,
    `image`       varchar(255) NOT NULL,
    `since`       datetime              DEFAULT NULL,
    `until`       datetime              DEFAULT NULL,
    `ordering`    int(11)      NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    KEY `idx_team` (`team_id`),
    KEY `idx_coach` (`coach_id`),
    KEY `idx_position` (`position_id`),
    CONSTRAINT `fk_coaches_teams_team_id` FOREIGN KEY (`team_id`) REFERENCES `#__footballmanager_teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_coaches_teams_coach_id` FOREIGN KEY (`coach_id`) REFERENCES `#__footballmanager_coaches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_coaches_teams_position_id` FOREIGN KEY (`position_id`) REFERENCES `#__footballmanager_positions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_positions`
(
    `id`             int(11)          NOT NULL AUTO_INCREMENT,
    `title`          varchar(255)     NOT NULL,
    `shortname`      varchar(10)               DEFAULT NULL,
    `alias`          varchar(255)     NOT NULL,
    `learnmore_link` varchar(255)              DEFAULT NULL,
    `description`    text                      DEFAULT NULL,
    `params`         text                      DEFAULT NULL,
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
    `catid`          int(11)          NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    CONSTRAINT `fk_positions_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_positions_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_players`
(
    `id`          int(11)          NOT NULL AUTO_INCREMENT,
    `firstname`   varchar(255)     NOT NULL,
    `lastname`    varchar(255)     NOT NULL,
    `alias`       varchar(255)     NOT NULL,
    `about`       text                      DEFAULT NULL,
    `height`      FLOAT                     DEFAULT NULL,
    'weight'      FLOAT                     DEFAULT NULL,
    'birthday'    DATE                      DEFAULT NULL,
    `nickname`    varchar(255)              DEFAULT NULL,
    `image`       varchar(255)              DEFAULT NULL,
    `params`      text                      DEFAULT NULL,
    `sponsors`    text                      DEFAULT NULL,
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
    CONSTRAINT `fk_players_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_players_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_players_teams`
(
    `id`            int(11)      NOT NULL AUTO_INCREMENT,
    `team_id`       int(11)               DEFAULT NULL,
    `player_id`     int(11)      NOT NULL,
    `player_number` int(11)      NOT NULL,
    `position_id`   int(11)               DEFAULT NULL,
    `image`         varchar(255) NOT NULL,
    `since`         datetime              DEFAULT NULL,
    `until`         datetime              DEFAULT NULL,
    `ordering`      int(11)      NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    KEY `idx_team` (`team_id`),
    KEY `idx_coach` (`player_id`),
    KEY `idx_position` (`position_id`),
    CONSTRAINT `fk_players_teams_team_id` FOREIGN KEY (`team_id`) REFERENCES `#__footballmanager_teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_players_teams_coach_id` FOREIGN KEY (`player_id`) REFERENCES `#__footballmanager_players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_players_teams_position_id` FOREIGN KEY (`position_id`) REFERENCES `#__footballmanager_positions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS `#__footballmanager_games`
(
    `id`                        int(11)          NOT NULL AUTO_INCREMENT,
    `alias`                     varchar(400)     NOT NULL,
    `season_id`                 int(11)                   DEFAULT NULL,
    `phase_id`                  int(11)                   DEFAULT NULL,
    `league_id`                 int(11)                   DEFAULT NULL,
    `home_team_id`              int(11)                   DEFAULT NULL,
    `away_team_id`              int(11)                   DEFAULT NULL,
    `location_id`               int(11)                   DEFAULT NULL,
    `home_score`                int(11)                   DEFAULT 0,
    `away_score`                int(11)                   DEFAULT 0,
    `home_touchdowns`           int(11)                   DEFAULT 0,
    `away_touchdowns`           int(11)                   DEFAULT 0,
    `matchday`                  int(11)                   DEFAULT NULL,
    `tickets_link`              varchar(400)              DEFAULT NULL,
    `kickoff`                   datetime                  DEFAULT NULL,
    `game_finished`             tinyint(1)                DEFAULT 0,
    `game_postponed`            tinyint(1)                DEFAULT 0,
    `game_canceled`             tinyint(1)                DEFAULT 0,
    `notes`                     text                      DEFAULT NULL,
    `description`               text                      DEFAULT NULL,
    `head_referee_id`           int(11)                   DEFAULT NULL,
    `referees`                  text                      DEFAULT NULL,
    `home_roster_offense`       text                      DEFAULT NULL,
    `home_roster_defense`       text                      DEFAULT NULL,
    `home_roster_special_teams` text                      DEFAULT NULL,
    `away_roster_offense`       text                      DEFAULT NULL,
    `away_roster_defense`       text                      DEFAULT NULL,
    `away_roster_special_teams` text                      DEFAULT NULL,
    `related_articles`          text                      DEFAULT NULL,
    `game_banner`               varchar(255)              DEFAULT NULL,
    `game_flyer`                varchar(255)              DEFAULT NULL,
    `supporting_games`          text                      DEFAULT NULL,
    `params`                    text                      DEFAULT NULL,
    `state`                     tinyint(3)       NOT NULL DEFAULT 0,
    `published`                 tinyint(1)       NOT NULL DEFAULT 0,
    `created_at`                datetime                  DEFAULT NULL,
    `created_by`                int(11)                   DEFAULT NULL,
    `modified_at`               datetime                  DEFAULT NOW(),
    `modified_by`               int(11)                   DEFAULT NULL,
    `version`                   int(11)                   DEFAULT 0,
    `hits`                      int(11)                   DEFAULT 0,
    `access`                    int(10) unsigned NOT NULL DEFAULT 0,
    `language`                  char(7)          NOT NULL DEFAULT '',
    `ordering`                  int(11)          NOT NULL DEFAULT 0,
    `catid`                     int(11)          NOT NULL DEFAULT 0,
    `sponsors`                  TEXT                      DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `idx_season` (`season_id`),
    KEY `idx_phase` (`phase_id`),
    KEY `idx_league` (`league_id`),
    KEY `idx_home_team` (`home_team_id`),
    KEY `idx_away_team` (`away_team_id`),
    KEY `idx_location` (`location_id`),
    CONSTRAINT `fk_games_season_id` FOREIGN KEY (`season_id`) REFERENCES `#__footballmanager_seasons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_phase_id` FOREIGN KEY (`phase_id`) REFERENCES `#__footballmanager_season_phases` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_league_id` FOREIGN KEY (`league_id`) REFERENCES `#__footballmanager_leagues` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_home_team_id` FOREIGN KEY (`home_team_id`) REFERENCES `#__footballmanager_teams` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_away_team_id` FOREIGN KEY (`away_team_id`) REFERENCES `#__footballmanager_teams` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_location_id` FOREIGN KEY (`location_id`) REFERENCES `#__footballmanager_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_created_by` FOREIGN KEY (`created_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_games_modified_by` FOREIGN KEY (`modified_by`) REFERENCES `#__users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
);