<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Overview;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use NXD\Component\Footballmanager\Administrator\Model\OverviewModel;

/**
 * Overview of FootballManager 2.0
 *
 * @since  2.0.0
 */
class HtmlView extends BaseHtmlView
{
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
		/** @var OverviewModel $model */
		$model         = $this->getModel();
		$this->version = $model->getComponentVersion();
		$this->addToolbar();

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
	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', false);
		ToolbarHelper::title(Text::_('COM_FOOTBALLMANAGER_OVERVIEW_TITLE'), 'fas fa-football-ball');
		ToolbarHelper::preferences('com_footballmanager');

	}
}
