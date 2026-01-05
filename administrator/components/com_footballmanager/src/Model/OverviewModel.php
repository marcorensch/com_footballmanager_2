<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\Model\AdminModel;
use NXD\Component\Footballmanager\Administrator\Helper\FootballmanagerHelper;

class OverviewModel extends AdminModel
{

	public $typeAlias = 'com_footballmanager.overview';
	public function getForm($data = [], $loadData = false)
	{
		// Get the form.
		$form = $this->loadForm($this->typeAlias, 'overview', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getComponentVersion(){
		return FootballmanagerHelper::getComponentVersion();
	}

}