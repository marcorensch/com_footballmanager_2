<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\View\Official;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use NXD\Component\Footballmanager\Administrator\Model\CoachModel;
use NXD\Component\Footballmanager\Administrator\Model\OfficialModel;
use NXD\Component\Footballmanager\Administrator\Model\RefereeModel;

/**
 * View to edit a foo.
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
	    /** @var OfficialModel $model */
	    $model      = $this->getModel();
	    $this->item = $model->getItem();

		// If we are forcing a language in modal (used for associations).
	    if($this->getLayout() === 'modal' && $forcedLanguage = Factory::getApplication()->input->get('forcedLanguage', '', 'cmd')){
			// Set the language field to the forcedLanguage and disable changing it.
		    $this->form->setValue('language', null, $forcedLanguage);
		    $this->form->setFieldAttribute('language', 'readonly', 'true');
//		    $this->form->setFieldAttribute('language', 'disabled', 'true');

			// Also, disable the "Associations" tab.
		    $this->form->setFieldAttribute('associations', 'readonly', 'true');
		    $this->form->setFieldAttribute('associations', 'disabled', 'true');

		    // Only allow to select categories with ALL language or with the forced language.
		    $this->form->setFieldAttribute('catid', 'language', '*,' . $forcedLanguage);
	    }

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
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

	    $isNew = !$this->item->id;
        $toolbar = $this->getDocument()->getToolbar();

	    ToolbarHelper::title($isNew ? Text::_('COM_FOOTBALLMANAGER_OFFICIAL_NEW_TITLE') : Text::_('COM_FOOTBALLMANAGER_OFFICIAL_EDIT_TITLE'), 'fas fa-id-badge');

        $toolbar->apply('official.apply');

	    $saveGroup = $toolbar->dropdownButton('save-group');

	    $saveGroup->configure(
		    function (Toolbar $childBar) {
			    $childBar->save('official.save');
			    $childBar->save2new('official.save2new');
			    $childBar->save2copy('official.save2copy');
		    }
	    );
        $toolbar->cancel('official.cancel', 'JTOOLBAR_CLOSE');
    }
}
