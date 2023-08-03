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

use Joomla\CMS\Component\ComponentHelper;

class ExportHelper extends ComponentHelper
{
	public static function exportToCsv($data = array(), $name = 'export'){

		if(empty($data)){
			return false;
		}

		$separator = ",";
		$enclosure = "\"";
		$escape = "\\";
		$eol   = "\n";
		$filename = $name . '.csv';
		$headers  = array_keys($data[0]);
		$output   = fopen('php://output', 'w');
		fputcsv($output, $headers, $separator, $enclosure, $escape, $eol);
		foreach ($data as $row)
		{
			fputcsv($output, $row);
		}

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		fclose($output);

		exit;

	}
}