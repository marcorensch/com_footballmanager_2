<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Installer;

\defined('_JEXEC') or die;


class FootballmanagerHelper extends ComponentHelper
{
	public static function getComponentVersion()
	{

		// Get the Version from the manifest file.
		$manifest = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR . '/components/com_footballmanager/footballmanager.xml');

		// Return the version.
		return $manifest['version'];

	}
}