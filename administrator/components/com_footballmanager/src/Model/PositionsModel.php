<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;


/**
 * Methods supporting a list of foo records.
 *
 * @since  2.0.0
 */
class PositionsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @see     \JControllerLegacy
     *
     * @since   2.0.0
     */
	public function __construct($config = array())
    {
		if(empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'catid', 'a.catid', 'category_title',
			);

			$assoc = Associations::isEnabled();
			if ($assoc) {
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
     * @since   2.0.0
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $db->quoteName(
				[
					'a.id', 'a.title', 'a.alias', 'a.access', 'a.created_at', 'a.created_by',
					'a.published', 'a.ordering', 'a.state',
				]
            )
        );
        $query->from($db->quoteName('#__footballmanager_positions','a'));

	    // Join over the asset groups.
	    $query->select($db->quoteName('ag.title', 'access_level'))
		    ->join(
			    'LEFT',
			    $db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
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

		// Join Over the position category
	    		$query->select($db->quoteName('c.title', 'category_title'))
			->join(
				'LEFT',
				$db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid')
			);

		// Filter by access level.
        if ($access = $this->getState('filter.access')) {
			$query->where($db->quoteName('a.access') . ' = ' . (int) $access);
        }

		// Filter by published state
	    // Filter by published state
	    $published = $this->getState('filter.published');
	    if (is_numeric($published)) {
		    $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
	    } elseif ($published === '*') {
		    // all filter selected
	    }else{
		    // none filter selected by default show published only
		    $query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
	    }

		// Filter by category.
	    $categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
		}

		// Filter by search name
	    $search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
			}
		}

	    // Add the list ordering clause.
	    $orderCol = $this->state->get('list.ordering', 'a.id');
	    $orderDirn = $this->state->get('list.direction', 'asc');

	    if ($orderCol == 'a.ordering')
	    {
		    $orderCol = $db->quoteName('a.ordering');
	    }
	    $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

	/**
	 * @throws \Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();
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
		if(!empty($forcedLanguage)){
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
		$query->from($db->quoteName('#__footballmanager_positions'));
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
