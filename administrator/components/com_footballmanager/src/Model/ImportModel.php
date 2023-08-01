<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

\defined('_JEXEC') or die;


use Joomla\CMS\MVC\Model\AdminModel;

class ImportModel extends AdminModel
{

	public $typeAlias = 'com_footballmanager.import';
	public function getForm($data = [], $loadData = false)
	{
		// Get the form.
		$form = $this->loadForm($this->typeAlias, 'import', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function import()
	{
		// yeah an import...nice
	}

}