/* UNINSTALL: Drop Tables in correct order due to Foreign Keys */

/* 1. First, the link tables (they have foreign keys) */
DROP TABLE IF EXISTS `#__footballmanager_players_countries`;
DROP TABLE IF EXISTS `#__footballmanager_coaches_countries`;
DROP TABLE IF EXISTS `#__footballmanager_officials_countries`;
DROP TABLE IF EXISTS `#__footballmanager_cheerleaders_countries`;
DROP TABLE IF EXISTS `#__footballmanager_players_teams`;
DROP TABLE IF EXISTS `#__footballmanager_coaches_teams`;

/* 2. Then the base tables that are referenced */
DROP TABLE IF EXISTS `#__footballmanager_games`;
DROP TABLE IF EXISTS `#__footballmanager_players`;
DROP TABLE IF EXISTS `#__footballmanager_coaches`;
DROP TABLE IF EXISTS `#__footballmanager_officials`;
DROP TABLE IF EXISTS `#__footballmanager_cheerleaders`;
DROP TABLE IF EXISTS `#__footballmanager_teams`;
DROP TABLE IF EXISTS `#__footballmanager_positions`;
DROP TABLE IF EXISTS `#__footballmanager_season_phases`;
DROP TABLE IF EXISTS `#__footballmanager_seasons`;
DROP TABLE IF EXISTS `#__footballmanager_leagues`;
DROP TABLE IF EXISTS `#__footballmanager_sponsors`;
DROP TABLE IF EXISTS `#__footballmanager_locations`;
DROP TABLE IF EXISTS `#__footballmanager_countries`;
