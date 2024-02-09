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

class CoachesModel extends BaseDatabaseModel
{

	/**
	 * @throws \Exception
	 */
	public function getItems(): array
	{
		$teamId = $this->getState('filter.teamId', null);
		$sortingDirection = $this->getState('sorting.direction', 'ASC');
		$orderBy = $this->getState('sorting.orderBy', 'ordering');

		$onlyCurrentTeam = $this->getState('filter.currentTeamOnly', '0');
		$onlyActivePositions = $this->getState('filter.activePositionsOnly', 0);


		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(
			array('p.id','p.firstname', 'p.lastname', 'p.image', 'p.about'),
			array('id','firstname', 'lastname', 'image', 'about')))
			->from($db->quoteName('#__footballmanager_coaches', 'p'));


		// Create a subquery for the team(s) DATA (only used for display)
		$subQuery = $db->getQuery(true);
		$subQuery->select('JSON_ARRAYAGG(JSON_OBJECT("title", t.title, "team_id", t.id, "registrationId", ct.id, "image", ct.image, "since", ct.since, "until", ct.until, "ordering", ct.ordering, "position", pos.title, "position_description", pos.description, "position_link", pos.learnmore_link)) as teams')
			->from($db->quoteName('#__footballmanager_coaches_teams', 'pt'))
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_teams', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('ct.team_id')
			)
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_positions', 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ct.position_id')
			)

			->where($db->quoteName('ct.coach_id') . ' = ' . $db->quoteName('p.id'));

		// Filter: Current Team only
		if ($onlyCurrentTeam)
		{
			$subQuery->where('(' . $db->quoteName('ct.team_id') . ' = ' . $db->quote($teamId) . ')');
		}

		// Display Filter for ACTIVE Positions only
		if ($onlyActivePositions)
		{
			$subQuery->where('(' . $db->quoteName('ct.since') . ' IS NULL OR ' . $db->quoteName('ct.since') . ' <= NOW())');
			$subQuery->where('(' . $db->quoteName('ct.until') . ' IS NULL OR ' . $db->quoteName('ct.until') . ' >= NOW())');
		}


		$query->select('(' . $subQuery . ') as teams');

		// <<< END OF SUBQUERY for team(s) DATA

		// Create a subquery for the Team & League Filter

		$subQueryFilterTeam = $db->getQuery(true);
		$subQueryFilterTeam->select('GROUP_CONCAT(' . $db->quoteName('ctf.team_id') . ' ORDER BY ' . $db->quoteName('ctf.ordering') . ' SEPARATOR ",")')
			->from($db->quoteName('#__footballmanager_coachs_teams', 'ctf'))
			->where($db->quoteName('ctf.coach_id') . ' = ' . $db->quoteName('p.id'));

		// filter for active Only
		if (true)
		{
			$subQueryFilterTeam->where('(' . $db->quoteName('ctf.since') . ' IS NULL OR ' . $db->quoteName('ctf.since') . ' <= NOW())');
			$subQueryFilterTeam->where('(' . $db->quoteName('ctf.until') . ' IS NULL OR ' . $db->quoteName('ctf.until') . ' >= NOW())');
		}

		// Modify the main query to include the nested query in the WHERE clause
		$query->where('FIND_IN_SET (' . $db->quote($teamId) . ', (' . $subQueryFilterTeam . '))');



		// Only Published Items
		$query->where($db->quoteName('p.published') . ' = ' . $db->quote('1'));

		// Order by Rules (Number & Position will be handled differently inside the module helper)
		if(!in_array($orderBy ,array('number','position')))
		{
			$query->order('p.' . $orderBy . ' ' . $sortingDirection);
		}

		$db->setQuery($query);
		$coaches = $db->loadObjectList();

		return $this->preparePeopleData($coaches);

	}

	/**
	 * @throws \Exception
	 */
	protected function preparePeopleData($coaches)
	{
		// component params
		$params = $this->getState('params');

		foreach ($coaches as $coach)
		{
			$coach->cfields = $this->getCustomFields($coach);
		}
		return $coaches;
	}

	/**
	 * @throws \Exception
	 */
	protected function getCustomFields($coach): array
	{
		$groupFields = $this->getState('params.groupByFieldGroups', []);
		$groups = array();

		// Get the associated fields
		$fields = FieldsHelper::getFields('com_footballmanager.coach', $coach, true);

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
}