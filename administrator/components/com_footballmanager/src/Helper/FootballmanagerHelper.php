<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Installer;
use Tobscure\JsonApi\Document;

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