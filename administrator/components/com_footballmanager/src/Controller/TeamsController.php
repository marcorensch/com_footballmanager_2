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

use JetBrains\PhpStorm\NoReturn;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;

/**
 * Foos list controller class.
 *
 * @since  __BUMP_VERSION__
 */
class TeamsController extends AdminController
{
    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * Recognized key values include 'name', 'default_task', 'model_path', and
     * 'view_path' (this list is not meant to be comprehensive).
     * @param   MVCFactoryInterface  $factory  The factory.
     * @param   CMSApplication       $app      The JApplication for the dispatcher
     * @param   Input                $input    Input
     *
     * @since   __BUMP_VERSION__
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
    }

    /**
     * Proxy for getModel.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  The prefix for the PHP class name.
     * @param   array   $config  Array of configuration parameters.
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     *
     * @since   __BUMP_VERSION__
     */
    public function getModel($name = 'team', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

	#[NoReturn] public function export(): void
	{
		error_log('export');

		$model = $this->getModel('teams');
		$data = $model->getItems();
		$filename = 'teams.csv';
		$csvData = '';
		$csvData .= 'id,catid,title,published,access,ordering,language' . "\n";
		foreach ($data as $item) {
			$csvData .= $item->id . ',' . $item->catid . ',' . $item->title . ',' . $item->published . ',' . $item->access . ',' . $item->ordering . ',' . $item->language . "\n";
		}

		error_log($csvData);

		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		echo $csvData;
		exit;
	}


}
