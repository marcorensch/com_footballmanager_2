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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

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
			case 'xls':
				self::exportXLS($data, $name);
				break;
			case 'xlsx':
				self::exportXLSX($data, $name);
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

	private static function exportXLS($data, $name): void
	{
		$spreadsheet     = new Spreadsheet();
		$activeWorksheet = $spreadsheet->getActiveSheet();
		$activeWorksheet->fromArray($data, null, 'A1');

		error_log(print_r($data, true));

		ob_clean();
		$writer   = new Xls($spreadsheet);
		$filename = $name . '.xls';

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		$writer->save('php://output');
		exit();
	}

	private static function exportXLSX($data, $name): void
	{

		$spreadsheet     = new Spreadsheet();
		$activeWorksheet = $spreadsheet->getActiveSheet();
		$activeWorksheet->fromArray($data, null, 'A1');

		ob_clean();
		$writer   = new Xlsx($spreadsheet);
		$filename = $name . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		$writer->save('php://output');

		exit();
	}
}