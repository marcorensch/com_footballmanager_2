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
class GamesModel extends ListModel
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
				'home_team', 'a.home_team_id',
				'away_team', 'a.away_team_id',
				'location', 'a.location_id',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'created_at', 'a.created_at',
				'modified_at', 'a.modified_at'
			);

			$assoc = Associations::isEnabled();
			if ($assoc)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
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
					'a.home_team_id', 'a.away_team_id', 'a.location_id',
					'a.state', 'a.published', 'a.created_at', 'a.created_by', 'a.modified_at', 'a.modified_by',
					'a.version', 'a.params', 'a.language', 'a.ordering', 'a.catid',
				]
			)
		);
		$query->from($db->quoteName('#__footballmanager_games', 'a'));

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

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access') . ' = ' . (int) $access);
		}

		// Filter by published state
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
				$query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
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

		return $items;
	}

	public function exportItems($ids)
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__footballmanager_games'));
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
