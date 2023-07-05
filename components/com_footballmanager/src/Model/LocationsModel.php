<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_foos
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Locations model for the Joomla Football Manager component.
 *
 * @since  __BUMP_VERSION__
 */
class LocationsModel extends BaseDatabaseModel
{
	/**
	 * @var string items
	 */
	protected $_items = null;

//	protected $_groups = null;

	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		parent::__construct($config, $factory);
//		$this->_groups = $this->getAllGroups();
	}

	/**
	 * Gets a list of locations
	 *
	 * @return  mixed Object or null
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function getItems()
	{
		$app = Factory::getApplication();
		$pk  = $app->input->getInt('id');

//		$categories_filter = Factory::getApplication()->getParams()->get('category_filter');

		if ($this->_items === null)
		{
			$this->_items = [];
		}

		if (!isset($this->_items[$pk]))
		{
			try
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select(array('a.*'))
					->from($db->quoteName('#__footballmanager_locations', 'a'))
					->where('a.published = 1');
//				if ($categories_filter)
//				{
//					$query->where('a.catid IN (' . implode(',', $categories_filter) . ')');
//				}
//				$query->join(
//					'LEFT',
//					$db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid')
//				)

				$query->order('a.title ASC');

				$db->setQuery($query);
				$locations = $db->loadObjectList();


				if (empty($locations))
				{
					return [];
				}

				$this->setFilters($locations);

				$this->_items[$pk] = $locations;
			}
			catch (\Exception $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}


		return $this->_items[$pk];
	}

	private function setFilters(&$locations)
	{
		foreach ($locations as $key => $location)
		{
			$location->filters = array();
		}
	}
}
