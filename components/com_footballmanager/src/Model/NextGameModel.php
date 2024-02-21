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

class NextGameModel extends BaseDatabaseModel
{

	public function getNextGame(): \stdClass|null
	{
		$teamIds = $this->getState('filter.teamIds', null);
		$leagueIds = $this->getState('filter.leagueIds', null);
		$limit = 1;

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

		// Filter League
		if ($leagueIds)
		{
			$query->where($db->quoteName('g.league_id') . ' IN (' . implode(',', $leagueIds) . ')');
		}


		$query->where($db->quoteName('g.kickoff') . ' >= NOW()');
		if($teamIds)
		{
			$query->andWhere('(' . $db->quoteName('g.home_team_id') . ' IN (' . implode(',', $teamIds) . ')');
			$query->orWhere($db->quoteName('g.away_team_id') . ' IN (' . implode(',', $teamIds) . '))');
		}

		// Only Published Games
		$query->andWhere($db->quoteName('g.published') . ' = ' . $db->quote('1'));

		// Order by Kickoff date
		$query->order($db->quoteName('g.kickoff') . ' ASC');

		$query->setLimit($limit, 0);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to get the games by ID.
	 *
	 * @param   array  $ids  ID's of the games to get. Array of integer id's
	 *
	 * @return  array           The game objects.
	 *
	 * @since   2.0.0
	 */
	public function getSupportGames(array $ids):array
	{
		if(empty($ids))
		{
			return array();
		}

		// Get all games where the ID is in the array of ID's and the game is published
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

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

		$query->where($db->quoteName('g.id') . ' IN (' . implode(',', $ids) . ')');
		$query->where($db->quoteName('g.published') . ' = ' . $db->quote('1'));
		$query->order($db->quoteName('g.kickoff') . ' ASC');
		$db->setQuery($query);
		return $db->loadObjectList();

	}
}