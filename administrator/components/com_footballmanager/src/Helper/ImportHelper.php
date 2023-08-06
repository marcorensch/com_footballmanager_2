<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Helper;

\defined('_JEXEC') or die;

use JetBrains\PhpStorm\NoReturn;
use Joomla\CMS\Component\ComponentHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
			case 'application/vnd.ms-excel':
//				self::importXLSX($file);
				break;
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
//				self::importXLSX($file);
				break;
			default:
				return false;
		}

		return true;

	}

}