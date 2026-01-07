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

class ExportHelper extends ComponentHelper
{
	public static function export($data = array(), $name = 'export')
	{

		if (empty($data))
		{
			return false;
		}

		$params        = ComponentHelper::getParams('com_footballmanager');
		$export_format = $params->get('export_format', 'csv');

		switch ($export_format)
		{
			case 'csv':
			default:
				self::exportCSV($data, $name);
				break;
		}

		return true;
	}

	#[NoReturn] private static function exportCSV($data, $name)
	{
		$separator = ",";
		$enclosure = "\"";
		$escape    = "\\";
		$eol       = "\n";
		$filename  = $name . '.csv';
		$headers   = array_keys($data[0]);
		$output    = fopen('php://output', 'w');
		fputcsv($output, $headers, $separator, $enclosure, $escape, $eol);
		foreach ($data as $row)
		{
			fputcsv($output, $row);
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		fclose($output);

		exit();
	}
}