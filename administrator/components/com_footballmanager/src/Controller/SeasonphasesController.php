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
use NXD\Component\Footballmanager\Administrator\Helper\ExportHelper;

/**
 * Foos list controller class.
 *
 * @since  __BUMP_VERSION__
 */
class SeasonphasesController extends AdminController
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
    public function getModel($name = 'seasonphase', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

	public function export()
	{
		$ids = $this->input->get('cid', [], 'array');
		$model = $this->getModel('seasonphases');
		$data  = $model->exportItems($ids);

		ExportHelper::export($data, 'seasonphases');
	}

	#[NoReturn] public function import(): void
	{
		$this->setRedirect('index.php?option=com_footballmanager&view=import&type=seasonphases');
	}


}