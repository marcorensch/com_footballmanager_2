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
		$season = $this->getState('filter.season', null);
		$league = $this->getState('filter.league', null);
		$teamId = $this->getState('filter.teamId', array());

		if(!$season) return array();

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('g.*')->from($db->quoteName('#__footballmanager_games', 'g'));

		// Filter Team ID's
		if (!empty($teamId))
		{
			$query->where($db->quoteName('g.home_team') . ' IN (' . implode(',', $teamId) . ')');
			$query->where($db->quoteName('g.away_team') . ' IN (' . implode(',', $teamId) . ')');
		}

		// SubQuery for Home Team
		$home = $db->getQuery(true);
		$home->select('JSON_ARRAYAGG(JSON_OBJECT("title", t.title, "logo", t.logo))')->from($db->quoteName('#__footballmanager_teams', 't'))
			->where('t.id = g.home_team');

		$query->select('(' . $home . ') as home');

		// SubQuery for Away Team
		$away = $db->getQuery(true);
		$away->select('JSON_ARRAYAGG(JSON_OBJECT("title", t.title, "logo", t.logo))')->from($db->quoteName('#__footballmanager_teams', 't'))
			->where('t.id = g.away_team');

		$query->select('(' . $away . ') as away');


		// Only Published Games
		$query->where($db->quoteName('g.published') . ' = ' . $db->quote('1'));

		$db->setQuery($query);
		$games = $db->loadObjectList();

		return $games;
	}

}