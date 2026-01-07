<?php
/**
 * @package     com_footballmanager
 *
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;

class MatchballModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'season',
				'league',
				'location',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   2.2.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

		// Filter Season - from menu parameters
		$filterSeason = $app->input->get('filter_season', null, 'array');
		if ($filterSeason === null) {
			$filterSeason = $app->getParams()->get('filter_season');
		}
		$this->setState('filter.season', $filterSeason);

		// Filter League - from menu parameters
		$filterLeague = $app->input->get('filter_league', null, 'array');
		if ($filterLeague === null) {
			$filterLeague = $app->getParams()->get('filter_league');
		}
		$this->setState('filter.league', $filterLeague);

		// Filter Location - from menu parameters
		$filterLocation = $app->input->get('filter_location', null, 'array');
		if ($filterLocation === null) {
			$filterLocation = $app->getParams()->get('filter_location');
		}
		$this->setState('filter.location', $filterLocation);

		parent::populateState($ordering, $direction);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   2.2.0
	 */
	protected function getListQuery(): \Joomla\Database\QueryInterface
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select([
			$db->quoteName('a.id'),
			$db->quoteName('a.kickoff'),
			$db->quoteName('a.matchball_sponsor_id'),
		])
			->from($db->quoteName('#__footballmanager_games', 'a'));

		// Join over the matchball sponsor
		$query->select([
			$db->quoteName('mb.title', 'matchball_sponsor_title'),
			$db->quoteName('mb.logo', 'matchball_sponsor_logo'),
			$db->quoteName('mb.image', 'matchball_sponsor_image'),
			$db->quoteName('mb.street', 'matchball_sponsor_street'),
			$db->quoteName('mb.city', 'matchball_sponsor_city'),
			$db->quoteName('mb.zip', 'matchball_sponsor_zip'),
			$db->quoteName('mb.website', 'matchball_sponsor_website'),
		])
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_sponsors', 'mb') . ' ON ' . $db->quoteName('mb.id') . ' = ' . $db->quoteName('a.matchball_sponsor_id')
			);

		// Join over the league
		$query->select($db->quoteName('l.title', 'league_title'))
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_leagues', 'l') . ' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('a.league_id')
			);

		// Join over the location name
		$query->select($db->quoteName('loc.title', 'location_title'))
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_locations', 'loc') . ' ON ' . $db->quoteName('loc.id') . ' = ' . $db->quoteName('a.location_id')
			);

		// Join over the home team name and short name
		$query->select([
			$db->quoteName('ht.title', 'home_team_title'),
			$db->quoteName('ht.shortname', 'home_team_short_name'),
			$db->quoteName('ht.shortcode', 'home_team_short_code'),
			$db->quoteName('ht.logo', 'home_team_logo'),
		])
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_teams', 'ht') . ' ON ' . $db->quoteName('ht.id') . ' = ' . $db->quoteName('a.home_team_id')
			);

		// Join over the away team name and short name
		$query->select([
			$db->quoteName('at.title', 'away_team_title'),
			$db->quoteName('at.shortname', 'away_team_short_name'),
			$db->quoteName('at.shortcode', 'away_team_short_code'),
			$db->quoteName('at.logo', 'away_team_logo'),
		])
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_teams', 'at') . ' ON ' . $db->quoteName('at.id') . ' = ' . $db->quoteName('a.away_team_id')
			);

		// Filter by Season
		$filterSeason = $this->getState('filter.season');
		if (!empty($filterSeason) && is_array($filterSeason))
		{
			$filterSeason = array_map('intval', $filterSeason);
			$query->where($db->quoteName('a.season_id') . ' IN (' . implode(',', $filterSeason) . ')');
		}

		// Filter by League
		$filterLeague = $this->getState('filter.league');
		if (!empty($filterLeague) && is_array($filterLeague))
		{
			$filterLeague = array_map('intval', $filterLeague);
			$query->where($db->quoteName('a.league_id') . ' IN (' . implode(',', $filterLeague) . ')');
		}

		// Filter by Location
		$filterLocation = $this->getState('filter.location');
		if (!empty($filterLocation) && is_array($filterLocation))
		{
			$filterLocation = array_map('intval', $filterLocation);
			$query->where($db->quoteName('a.location_id') . ' IN (' . implode(',', $filterLocation) . ')');
		}

		// Filter by Published Games
		$query->where($db->quoteName('a.published') . ' = ' . $db->quote('1'));

		$query->order($db->quoteName('a.kickoff') . ' ASC');

		return $query;
	}
}