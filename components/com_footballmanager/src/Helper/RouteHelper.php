<?php
/**
 * * @package     NXD.FootballManager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

/**
 * FootballManager Component Route Helper
 *
 * @static
 * * @package     NXD.FootballManager
 * 
 *
 * @since  1.0.0
 */

abstract class RouteHelper{
	/**
	 * Get the URL route for locations from a location ID, locations category ID and language
	 *
	 * @param   integer  $id        The id of the location
	 * @param   integer  $catid     The id of the category
	 * @param   mixed    $language  The language being used
	 *
	 * @return  string             The URL route for the location
	 *
	 * @since  1.0.0
	 */

	public static function getLocationsRoute($id, $catid, $language = 0){
		// Create the link
		$link = 'index.php?option=com_footballmanager&view=locations&id=' . $id;
		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}
		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}
		return $link;
	}

	/**
	 * Get the URL route for a location from a location ID, locations category ID and language
	 *
	 * @param   integer  $id        The id of the location
	 * @param   integer  $catid     The id of the location's category
	 * @param   mixed    $language  The language being used
	 *
	 * @return string              The URL route for the location
	 *
	 * @since  1.0.0
	 *
	 */
	public static function getLocationrRoute($id, $catid, $language = 0){
		// Create the link
		$link = 'index.php?option=com_footballmanager&view=location&id=' . $id;
		if ($catid > 1) {
			$link .= '&catid=' . $catid;
		}
		if ($language && $language !== '*' && Multilanguage::isEnabled()) {
			$link .= '&lang=' . $language;
		}
		return $link;
	}

	/**
	 * Get the URL route for a location category from a category ID and language
	 *
	 * @param mixed $catid The id of the category
	 * @param mixed $language The language being used
	 *
	 * @return string The URL route for the category
	 *
	 * @since 1.0.0
	 *
	 */
	public static function getCategoryRoute($catid, $language = 0){
		if($catid instanceof CategoryNode){
			$id = $catid->id;
		} else {
			$id = (int) $catid;
		}
		if($id < 1){
			$link = '';
		} else {
			$link = 'index.php?option=com_footballmanager&view=category&id=' . $id;
			if($language && $language !== '*' && Multilanguage::isEnabled()){
				$link .= '&lang=' . $language;
			}
		}

		return $link;
	}
}