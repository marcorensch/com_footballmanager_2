<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Import;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use NXD\Component\Footballmanager\Administrator\Model\ImportModel;

/**
 * View for Import.
 *
 * @since  2.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The \JForm object
     *
     * @var  \JForm
     */
    protected $form;

    /**
     * The active content
     *
     * @var  object
     */
    protected $item;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 * @throws \Exception
	 * @since 1.0.0
	 *
	 */
    public function display($tpl = null)
    {
	    /** @var ImportModel $model */
	    $model      = $this->getModel();


        $this->addToolbar();
		$this->openSidebar();

        return parent::display($tpl);
    }

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 * @since   2.0.0
	 */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', false);

	    $toolbar = Toolbar::getInstance();

	    ToolbarHelper::title(Text::_('COM_FOOTBALLMANAGER_IMPORT_TITLE'), 'fas fa-file-import');

    }

	protected function openSidebar()
	{
//		$app = Factory::getApplication();
//		$menu = $app->input->get('menu', 'mainmenu');
//		$items = $menu->getItems('component', 'com_footballmanager', true);
//		$app->setUserState('com_footballmanager.import.itemid', $itemId);
	}
}
