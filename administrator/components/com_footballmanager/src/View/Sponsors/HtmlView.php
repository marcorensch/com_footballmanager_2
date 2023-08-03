<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Sponsors;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;

/**
 * View class for a list of locations.
 *
 * @since  __BUMP_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Method to display the view.
	 *
	 * @param   string|null  $tpl  A template file to load. [optional]
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;


	public function display($tpl = null): void
	{
		$this->items            = $this->get('Items');
		$this->pagination       = $this->get('Pagination');
		$this->filterForm       = $this->get('FilterForm');
		$this->activeFilters    = $this->get('ActiveFilters');
		$this->state            = $this->get('State');

		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		if (!count($this->items) && $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		// We don't need toolbar in the modal window
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = \JHtmlSidebar::render();
		}
		else
		{
			// In article associations modal we need to remove language filter if forcing a language.
			// We also need to change the category filter to show categories with All or the forced language.
			if ($forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'cmd'))
			{
				// If the language is forced we can't allow to select the language, so transform the language selector filter into an hidden field.
				$languageXml = new \SimpleXMLElement('<field name="language" type="hidden" default="' . $forcedLanguage . '" />');
				$this->filterForm->setField($languageXml, 'filter', true);
				// Also, unset the active language filter so the search tools is not open by default with this filter.
				unset($this->activeFilters['language']);
				// One last changes needed is to change the category filter to just show categories with All language or with the forced language.
				$this->filterForm->setFieldAttribute('category_id', 'language', '*,' . $forcedLanguage, 'filter');
			}
		}

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		$this->sidebar = Sidebar::render();
		$canDo         = ContentHelper::getActions('com_footballmanager', 'category', $this->state->get('filter.category_id'));
		$user          = Factory::getApplication()->getIdentity();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();
		ToolbarHelper::title(Text::_('COM_FOOTBALLMANAGER_SPONSORS'), 'fas fa-handshake');

		// Show Buttons only if the user is allowed to do so
		if ($canDo->get('core.create') || count($user->getAuthorisedCategories('com_footballmanager', 'core.create')) > 0)
		{
			$toolbar->addNew('sponsor.add');
		}
		if ($canDo->get('core.edit.state'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('sponsors.publish')->listCheck(true);
			$childBar->unpublish('sponsors.unpublish')->listCheck(true);
			$childBar->archive('sponsors.archive')->listCheck(true);


			if ($user->authorise('core.admin'))
			{
				$childBar->checkin('sponsors.checkin')->listCheck(true);
			}

			if ($this->state->get('filter.published') != -2)
			{
				$childBar->trash('sponsors.trash')->listCheck(true);
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			$toolbar->delete('sponsors.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		// Add import / export buttons
		if ($user->authorise('core.edit'))
		{
			ToolbarHelper::custom('sponsors.import', 'upload', '', 'COM_FOOTBALLMANAGER_IMPORT', false);
			ToolbarHelper::custom('sponsors.export', 'download', '', 'COM_FOOTBALLMANAGER_EXPORT', true);
		}

		if ($user->authorise('core.admin', 'com_footballmanager') || $user->authorise('core.options', 'com_footballmanager'))
		{
			$toolbar->preferences('com_footballmanager');
		}
	}
}
