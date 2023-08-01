<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Import Controller
 *
 * @since  __BUMP_VERSION__
 */

class ImportController extends AdminController
{
	public function getModel($name = 'import', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function import()
	{
		$model = $this->getModel();
		$model->import();
		$this->setRedirect('index.php?option=com_footballmanager&view=teams');
	}
}