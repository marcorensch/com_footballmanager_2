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

class PlayersModel extends BaseDatabaseModel
{

	public function getItems(): array
	{
		$teamId = $this->getState('filter.teamId', null);
		$leagueIds = $this->getState('filter.leagueIds', null);
		$onlyCurrentTeam = $this->getState('filter.currentTeamOnly', '0');
		$onlyActivePositions = $this->getState('filter.activePositionsOnly', 0);
		$sortingDirection = $this->getState('sorting.direction', 'ASC');
		$orderBy = $this->getState('sorting.orderBy', 'ordering');

		if(!$teamId) return array();

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(
			array('p.id','p.firstname', 'p.lastname', 'p.nickname','p.image','p.height','p.weight', 'p.sponsors', 'p.about', 'p.birthday', 'p.country_id'),
			array('id','firstname', 'lastname','nickname', 'image', 'height', 'weight','sponsor_ids', 'about', 'birthdate', 'country_id')))
			->from($db->quoteName('#__footballmanager_players', 'p'));

		// Join over the country
		$query->select('c.title as country')
			->join('LEFT', $db->quoteName('#__footballmanager_countries', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('p.country_id'));

		// Create a subquery for the team(s) DATA (only used for display)
		$subQuery = $db->getQuery(true);
		$subQuery->select('JSON_ARRAYAGG(JSON_OBJECT("title", t.title, "team_id", t.id, "team_color", t.color, "registrationId", pt.id, "image", pt.image, "number", pt.player_number, "since", pt.since, "until", pt.until, "ordering", pt.ordering, "position", pos.title, "position_description", pos.description, "position_link", pos.learnmore_link, "league" , l.title)) as teams')
			->from($db->quoteName('#__footballmanager_players_teams', 'pt'))
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_teams', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id')
			)
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_positions', 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('pt.position_id')
			)
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_leagues', 'l') . ' ON ' . $db->quoteName('l.id') . ' = ' . $db->quoteName('pt.league_id')
			)
			->where($db->quoteName('pt.player_id') . ' = ' . $db->quoteName('p.id'));

		// Filter: Current Team only
		if ($onlyCurrentTeam)
		{
			$subQuery->where('(' . $db->quoteName('pt.team_id') . ' = ' . $db->quote($teamId) . ')');
		}

		// Display Filter for ACTIVE Positions only
		if ($onlyActivePositions)
		{
			$subQuery->where('(' . $db->quoteName('pt.since') . ' IS NULL OR ' . $db->quoteName('pt.since') . ' <= NOW())');
			$subQuery->where('(' . $db->quoteName('pt.until') . ' IS NULL OR ' . $db->quoteName('pt.until') . ' >= NOW())');
		}


		$query->select('(' . $subQuery . ') as teams');

		// <<< END OF SUBQUERY for team(s) DATA

		// Create a subquery for the Team & League Filter

		$subQueryFilterTeam = $db->getQuery(true);
		$subQueryFilterTeam->select('GROUP_CONCAT(' . $db->quoteName('ptf.team_id') . ' ORDER BY ' . $db->quoteName('ptf.ordering') . ' SEPARATOR ",")')
			->from($db->quoteName('#__footballmanager_players_teams', 'ptf'))
			->where($db->quoteName('ptf.player_id') . ' = ' . $db->quoteName('p.id'));

		// Filter: League IDs
		if ($leagueIds)
		{
			$subQueryFilterTeam->where('(' . $db->quoteName('ptf.league_id') . ' IN (' . implode(',', $leagueIds) . '))');
		}

		// Filter for active Only
		if (true)
		{
			$subQueryFilterTeam->where('(' . $db->quoteName('ptf.since') . ' IS NULL OR ' . $db->quoteName('ptf.since') . ' <= NOW())');
			$subQueryFilterTeam->where('(' . $db->quoteName('ptf.until') . ' IS NULL OR ' . $db->quoteName('ptf.until') . ' >= NOW())');
		}

		// Modify the main query to include the nested query in the WHERE clause
		$query->where('FIND_IN_SET (' . $db->quote($teamId) . ', (' . $subQueryFilterTeam . '))');


		// Only Published Players
		$query->where($db->quoteName('p.published') . ' = ' . $db->quote('1'));

		// Order by Rules (Number & Position will be handled differently inside the module helper)
		if(!in_array($orderBy ,array('number','position')))
		{
			$query->order('p.' . $orderBy . ' ' . $sortingDirection);
		}

		$db->setQuery($query);
		$players = $db->loadObjectList();

		return $this->preparePlayersData($players);
	}

	protected function preparePlayersData($players)
	{
		// component params
		$params = $this->getState('params');
		$heightUnit = $params->get('height_unit', 'cm');
		$weightUnit = $params->get('weight_unit', 'kg');

		foreach ($players as $player)
		{
			$player->weight = $player->weight ? $player->weight . ' ' . $weightUnit : null;
			$player->height = $player->height ? $player->height . ' ' . $heightUnit : null;
			$player->teams = $player->teams ? json_decode($player->teams) : null;
			$player->sponsors = $this->getSponsors($player);
			$player->cfields = $this->getCustomFields($player);
		}
		return $players;
	}

	/**
	 * @throws \Exception
	 */
	protected function getCustomFields($player): array
	{
		$groupFields = $this->getState('params.groupByFieldGroups', []);
		$groups = array();

		// Get the associated fields
		$fields = FieldsHelper::getFields('com_footballmanager.player', $player, true);

		// Group the fields by field group if requested.
		if ($groupFields)
		{
			return $this->groupFields($fields, $groupFields);
		}

		return $fields;

	}

	/**
	 * Group fields by field group if requested. returns the manipulated array containing the key groups of field groups containing the fields and the key fields containing the fields that are not in a group
	 * @param $fields           array       of fields
	 * @param $groupFields      array       of field group ids that should be grouped
	 *
	 * @since 1.0.0
	 * @return array
	 */
	private function groupFields($fields, $groupFields): array
	{
		$groups = array();
		$index = 0;
		foreach ($fields as $field)
		{
			if(in_array($field->group_id, $groupFields)){
				$groups[$field->group_id][] = $field;
				// remove field from fields array
				unset($fields[$index]);
			}
			$index++;
		}
		$customFields = array(
			'fields' => $fields,
			'groups' => $groups
		);

		return $customFields;
	}

	protected function getSponsors($sponsors)
	{
		$sponsors = json_decode($sponsors->sponsor_ids);

		$sponsorIds = array();
		foreach ($sponsors as $sponsor)
		{
			$sponsorIds[] = $sponsor->sponsor;
		}

		if(empty($sponsorIds)) return null;

		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('id, title, logo, image, website')
			->from('#__footballmanager_sponsors')
			->where('id IN (' . implode(',', $sponsorIds) . ')');
		$db->setQuery($query);
		return $db->loadObjectList();
	}

}