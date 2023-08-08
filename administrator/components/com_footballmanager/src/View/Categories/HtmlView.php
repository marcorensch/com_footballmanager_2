<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Categories;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{

	protected $sections;

	public function display($tpl = null): void
	{
		$this->sections = $this->get('Sections');

		$this->addToolbar();

		// We don't need toolbar in the modal window
		if ($this->getLayout() !== 'modal')
		{
			$this->sidebar = Sidebar::render();
		}

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_FOOTBALLMANAGER_CATEGORIES_LABEL'), 'folder');
		ToolbarHelper::preferences('com_footballmanager');
	}

}