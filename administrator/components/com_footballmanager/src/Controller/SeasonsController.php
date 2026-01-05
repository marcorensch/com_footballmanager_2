<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Controller;

\defined('_JEXEC') or die;

use JetBrains\PhpStorm\NoReturn;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Input\Input;
use NXD\Component\Footballmanager\Administrator\Helper\ExportHelper;
use NXD\Component\Footballmanager\Administrator\Helper\ImportHelper;
use Joomla\CMS\Language\Text;

/**
 * Foos list controller class.
 *
 * @since  2.0.0
 */
class SeasonsController extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @param   array                $config   An optional associative array of configuration settings.
	 *                                         Recognized key values include 'name', 'default_task', 'model_path', and
	 *                                         'view_path' (this list is not meant to be comprehensive).
	 * @param   MVCFactoryInterface  $factory  The factory.
	 * @param   CMSApplication       $app      The JApplication for the dispatcher
	 * @param   Input                $input    Input
	 *
	 * @since   2.0.0
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
	 * @since   2.0.0
	 */
	public function getModel($name = 'season', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function export()
	{
		$ids = $this->input->get('cid', [], 'array');
		$model = $this->getModel('seasons');
		$data  = $model->exportItems($ids);

		ExportHelper::export($data, 'seasons');
	}

	#[NoReturn] public function import(): void
	{
		// Get the uploaded file data from the Input class
		$app = Factory::getApplication();
		// Get the uploaded file data from the input
		$input = Factory::getApplication()->input;
		// Get the uploaded file data
		$files = $input->files->get('jform', array(), 'array');
		// Get the data from the form
		$data = $input->get('jform', array(), 'array');
		$forced = $data['forced_import'];
		$linked = $data['linked_elements'];


		// Check if the file was uploaded successfully (error code 0 means success)
		if ($files['upload_file']) {
			$file = $files['upload_file'];
			// Get the temporary file path
//			$tmpFilePath = $files['upload_file']['tmp_name'];

			// Get the original file name
			$fileName = $files['upload_file']['name'];

			// Do something with the file, e.g., move it to a desired location
//			$destinationPath = 'path/to/your/desired/location/' . $fileName;
//			move_uploaded_file($tmpFilePath, $destinationPath);

			// ... Process the uploaded file further as needed ...
			ImportHelper::import($file);

			// Redirect or return a response after processing
			$app->enqueueMessage(Text::sprintf('COM_FOOTBALLMANAGER_TEXT_UPLOAD_SUCCESS', $fileName ), 'message');
			$app->enqueueMessage(Text::sprintf('COM_FOOTBALLMANAGER_TEXT_IMPORT_SUCCESS', $fileName ), 'message');
		} else {
			// Handle the upload error
			// @TODO
			// Redirect or return a response to show the error to the user
			$app->enqueueMessage('File upload error', 'error');
		}

		$app->enqueueMessage('Importing seasons is not supported yet', 'warning');
		$this->setRedirect(Route::_('index.php?option=com_footballmanager&view=seasons', false));
	}


}
