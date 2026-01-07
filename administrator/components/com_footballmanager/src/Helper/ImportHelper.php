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

use JetBrains\PhpStorm\NoReturn;
use Joomla\CMS\Component\ComponentHelper;

class ImportHelper extends ComponentHelper
{

	public static function import($file = null)
	{
		if(empty($file))
		{
			return false;
		}

		// Switch method based on file type
		$type = $file['type'];

		switch ($type)
		{
			case 'text/csv':
//				self::importCSV($file);
				break;
			default:
				return false;
		}

		return true;

	}

}