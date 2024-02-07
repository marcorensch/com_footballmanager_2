<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;

class GamesModel extends BaseDatabaseModel
{

	public function getItems(): array
	{
		$teamId = $this->getState('filter.teamId', null);
		$seasonId = $this->getState('filter.seasonId', null);
		$leagueId = $this->getState('filter.leagueId', null);
		$onlyUpcoming = $this->getState('filter.onlyUpcoming', 0);
		$limit = $this->getState('filter.limit', 50);
		$direction = $this->getState('filter.direction', 'ASC');

		if (!$seasonId) return array();

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->clear();

		$query->select('g.*')->from($db->quoteName('#__footballmanager_games', 'g'));

		// Join (get) the League Title and  League alias from leagues table
		$query->select(array('league.title as league_title', 'league.alias as league_alias'))
			->join('LEFT', $db->quoteName('#__footballmanager_leagues', 'league') . ' ON ' . $db->quoteName('league.id') . ' = ' . $db->quoteName('g.league_id'));

		// Join Location Information
		$query->select('l.title as location_title')
			->join('LEFT', $db->quoteName('#__footballmanager_locations', 'l') . ' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('g.location_id'));

		$JsonObject = 'JSON_OBJECT("title", t.title, "logo", t.logo, "shortcode", shortcode, "shortname", shortname,"color", color)';

		// SubQuery for Home Team
		$home = $db->getQuery(true);
		$home->select('JSON_ARRAYAGG('.$JsonObject.')')->from($db->quoteName('#__footballmanager_teams', 't'))
			->where('t.id = g.home_team_id');

		$query->select('(' . $home . ') as home');

		// SubQuery for Away Team
		$away = $db->getQuery(true);
		$away->select('JSON_ARRAYAGG('.$JsonObject.')')->from($db->quoteName('#__footballmanager_teams', 't'))
			->where('t.id = g.away_team_id');

		$query->select('(' . $away . ') as away');

		// Filter Season
		$query->where($db->quoteName('g.season_id') . ' IN (' . implode(',', $seasonId) . ')' );

		// Filter League
		if ($leagueId)
		{
			$query->where($db->quoteName('g.league_id') . ' IN (' . implode(',', $leagueId) . ')');
		}

		// Filter Upcoming Games
		if ($onlyUpcoming)
		{
			$query->where($db->quoteName('g.kickoff') . ' >= NOW()');
		}

		// Filter Team ID's - Select Only Games where the team is involved as Home OR Away Team
		if (!empty($teamId))
		{
			$query->andWhere('(' . $db->quoteName('g.home_team_id') . ' IN (' . implode(',', $teamId) . ')');
			$query->orWhere($db->quoteName('g.away_team_id') . ' IN (' . implode(',', $teamId) . '))');
		}

		// Only Published Games
		$query->andWhere($db->quoteName('g.published') . ' = ' . $db->quote('1'));

		// Order by Kickoff date
		$query->order($db->quoteName('g.kickoff') . ' ' . $direction);

		// Set Limit
		if($limit) {
			$query->setLimit($limit, 0);
		}


		$db->setQuery($query);
		$games = $db->loadObjectList();

		return $games;
	}

}