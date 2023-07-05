<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_foos
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace NXD\Component\Footballmanager\Site\Helper;

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper;

/**
 * Foos Component Association Helper
 *
 * @since  1.0.0
 */
abstract class AssociationHelper extends CategoryAssociationHelper
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer  $id    Id of the item
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  1.0.0
	 */
	public static function getAssociations($id = 0, $view = null)
	{
		$jinput = Factory::getApplication()->input;
		$view = $view ?? $jinput->get('view');
		$id = empty($id) ? $jinput->getInt('id') : $id;

		if ($view === 'locations') {
			if ($id) {
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_locations', 'com_footballmanager.location', $id);
				$return = [];

				foreach ($associations as $tag => $item) {
					$return[$tag] = RouteHelper::getCompanypartnersRoute($item->id, (int) $item->catid, $item->language);
				}
				return $return;
			}
		}
		if ($view === 'category' || $view === 'categories') {
			return self::getCategoryAssociations($id, 'com_footballmanager');
		}
		return [];
	}
}