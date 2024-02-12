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

class CheerleadersModel extends BaseDatabaseModel
{

	/**
	 * @throws \Exception
	 */
	public function getItems(): array
	{
		$teamId = $this->getState('filter.teamId', null);
		$sortingDirection = $this->getState('sorting.direction', 'ASC');
		$orderBy = $this->getState('sorting.orderBy', 'ordering');

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName(
			array('p.id','p.firstname', 'p.lastname', 'p.image','p.height','p.weight', 'p.about', 'p.birthday', 'p.country_id'),
			array('id','firstname', 'lastname', 'image', 'height', 'weight', 'about', 'birthdate', 'country_id')))
			->from($db->quoteName('#__footballmanager_cheerleaders', 'p'));

		// Join Country
		$query->select('c.title as country')
			->join('LEFT', $db->quoteName('#__footballmanager_countries', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('p.country_id'));

		// Join Position
		$query->select('pos.title as position')
			->join('LEFT', $db->quoteName('#__footballmanager_positions', 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('p.position_id'));

		// Filter for Team
		if($teamId){
			$query->where($db->quoteName('p.linked_team_id') . ' = ' . $db->quote($teamId));
		}

		// Only Published Items
		$query->where($db->quoteName('p.published') . ' = ' . $db->quote('1'));

		// Order by Rules (Number & Position will be handled differently inside the module helper)
		if(!in_array($orderBy ,array('number','position')))
		{
			$query->order('p.' . $orderBy . ' ' . $sortingDirection);
		}

		$db->setQuery($query);
		$cheerleaders = $db->loadObjectList();

		return $this->preparePeopleData($cheerleaders);

	}

	/**
	 * @throws \Exception
	 */
	protected function preparePeopleData($cheerleaders)
	{
		// component params
		$params = $this->getState('params');
		$heightUnit = $params->get('height_unit', 'cm');
		$weightUnit = $params->get('weight_unit', 'kg');

		foreach ($cheerleaders as $cheerleader)
		{
			$cheerleader->weight = $cheerleader->weight ? $cheerleader->weight . ' ' . $weightUnit : null;
			$cheerleader->height = $cheerleader->height ? $cheerleader->height . ' ' . $heightUnit : null;
			$cheerleader->cfields = $this->getCustomFields($cheerleader);
		}
		return $cheerleaders;
	}

	/**
	 * @throws \Exception
	 */
	protected function getCustomFields($cheerleader): array
	{
		$groupFields = $this->getState('params.groupByFieldGroups', []);
		$groups = array();

		// Get the associated fields
		$fields = FieldsHelper::getFields('com_footballmanager.cheerleader', $cheerleader, true);

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