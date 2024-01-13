<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;


/**
 * Methods supporting a list of foo records.
 *
 * @since  __BUMP_VERSION__
 */
class PlayersModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'catid', 'a.catid',
				'firstname', 'a.firstname',
				'lastname', 'a.lastname',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'author_name', 'a.author_name',
				'created_at', 'a.created_at',
			);

			$assoc = Associations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}

	protected function filteredByTeamListQuery(){

	}

	protected function defaultListQuery(){

	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\QueryInterface
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getListQuery()
	{

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.

		$query->select(
			$db->quoteName(
				[
					'a.id', 'a.alias',
					'a.firstname', 'a.lastname', 'a.about', 'a.image',
					'a.state', 'a.published', 'a.created_at', 'a.created_by', 'a.modified_at', 'a.modified_by',
					'a.version', 'a.params', 'a.language', 'a.ordering', 'a.catid'
				]
			)
		);
		$query->from($db->quoteName('#__footballmanager_players', 'a'));

		// Join over the asset groups.
		$query->select($db->quoteName('ag.title', 'access_level'))
			->join(
				'LEFT',
				$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
			);

		// Join over the category.
		$query->select($db->quoteName('c.title', 'category_title'))->join(
			'LEFT',
			$db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid')
		);

		// Join over the author user
		$query->select($db->quoteName('u.name', 'author_name'))
			->join(
				'LEFT',
				$db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.created_by')
			);

		$query->select($db->quoteName('mod.name', 'modified_by_username'))
			->join(
				'LEFT',
				$db->quoteName('#__users', 'mod') . ' ON ' . $db->quoteName('mod.id') . ' = ' . $db->quoteName('a.modified_by')
			);

		// Filter by Team ID
		$filterTeamId = $this->getState('filter.team_id');



		// Subquery all teams for a player and use them as array in the player object as "teams"
		$subQuery = $db->getQuery(true);
		$subQuery->select('JSON_ARRAYAGG(JSON_OBJECT("title", t.title, "team_id", t.id, "since", pt.since, "until", pt.until, "ordering", pt.ordering)) as teams')
			->from($db->quoteName('#__footballmanager_players_teams', 'pt'))
			->join(
				'LEFT',
				$db->quoteName('#__footballmanager_teams', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id')
			)
			->where($db->quoteName('pt.player_id') . ' = ' . $db->quoteName('a.id'));

		$query->select('(' . $subQuery . ') as teams');

		if (is_numeric($filterTeamId)) {
			// Select teamIds using a nested query
			$subQueryFilterTeam = $db->getQuery(true);
			$subQueryFilterTeam->select('GROUP_CONCAT(DISTINCT ' . $db->quoteName('ptf.team_id') . ' ORDER BY ' . $db->quoteName('ptf.ordering') . ' SEPARATOR ",") AS teamIds')
				->from($db->quoteName('#__footballmanager_players_teams', 'ptf'))
				->where($db->quoteName('ptf.player_id') . ' = ' . $db->quoteName('a.id'));

			// Modify the main query to include the nested query in the WHERE clause
			$query->where($db->quote($filterTeamId) . ' IN (' . $subQueryFilterTeam . ')');








//			$subQuery->where($db->quoteName('pt.team_id') . ' = ' . $db->quote($filterTeamId));
//
//			// Switch for only active players by checking current date against team_since and team_until but only if pt.since and pt.until are not null
//			$onlyActive = $this->getState('filter.only_active');
//			if ($onlyActive)
//			{
//				$subQuery->where('(' . $db->quoteName('pt.since') . ' IS NULL OR ' . $db->quoteName('pt.since') . ' <= NOW())');
//				$subQuery->where('(' . $db->quoteName('pt.until') . ' IS NULL OR ' . $db->quoteName('pt.until') . ' >= NOW())');
//			}
		}


		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access') . ' = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '*')
		{
			// all filter selected
		}
		else
		{
			// none filter selected by default show published only
			$query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
		}
		elseif (is_array($categoryId))
		{
			$categoryId = ArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where($db->quoteName('a.catid') . ' IN (' . $categoryId . ')');
		}

		// Filter by search name
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.firstname LIKE ' . $search .' OR a.lastname LIKE ' . $search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.created_at');
		$orderDirn = $this->state->get('list.direction', 'desc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = $db->quoteName('c.title') . ' ' . $orderDirn . ', ' . $db->quoteName('a.ordering');
		}
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * @throws \Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app            = Factory::getApplication();
		$forcedLanguage = $app->input->get('forcedLanguage', '', 'cmd');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Adjust the context to support forced languages.
		if (!empty($forcedLanguage))
		{
			$this->context .= '.' . $forcedLanguage;
		}

		// List state information
		parent::populateState($ordering, $direction);

		// Force a language
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
		}
	}

	public function getItems()
	{
		$items = parent::getItems();
		echo '<pre>' . var_export($items, true) . '</pre>';
		// JSON Decode Teams
		foreach ($items as &$item)
		{
			$item->teams = $item->teams !== NULL ? json_decode($item->teams) : [];
			// order teams by ordering
			usort($item->teams, function($a, $b) {
				return $a->ordering <=> $b->ordering;
			});
		}
		return $items;

	}

	public function exportItems($ids)
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__footballmanager_players'));
		$query->where($db->quoteName('id') . ' IN (' . implode(',', $ids) . ')');
		$db->setQuery($query);
		return $db->loadAssocList();
	}

	public function getImportform($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_footballmanager.import', 'import', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
}
