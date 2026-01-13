<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Categories;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{

	protected $sections;

	public function display($tpl = null): void
	{
        /* @var \NXD\Component\Footballmanager\Administrator\Model\CategoriesModel $model */
        $model = $this->getModel();
		$this->sections = $model->getSections();

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