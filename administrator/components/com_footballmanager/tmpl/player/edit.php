<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use NXD\Component\Footballmanager\Administrator\View\Player\HtmlView;

/** @var HtmlView $this */

$app   = Factory::getApplication();
$input = $app->input;

$assoc = Associations::isEnabled();

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$isModal = $input->get('layout') === 'modal';

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

$wa->addInlineScript('
let $teamAddButtons = null;
let $teamsTab = null;
let $subFormRepeatableContainer = null;

document.addEventListener("DOMContentLoaded", function() {
    $teamsTab = document.querySelector("joomla-tab-element#teams");
    $teamsTab.addEventListener("subform-row-add", function() {
        updateOrderingValues();
    });
    $teamsTab.addEventListener("joomla:updated", function() {
        // console.log("joomla updated");
    });
    
    // Select the element you want to watch
    $subFormRepeatableContainer = document.querySelector(".subform-layout");
    
    $subFormRepeatableContainer.addEventListener("dragend", function() {
        updateOrderingValues();
    });
 
});

function updateOrderingValues(){
    let $subFormRows = $subFormRepeatableContainer.querySelectorAll(".subform-repeatable-group");
        console.log($subFormRows);
        for (let i = 0; i < $subFormRows.length; i++){
            let $row = $subFormRows[i];
            let $orderingInput = $row.querySelector("input.ordering");
            $orderingInput.value = i+1;
        }
};

');

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

?>
<form action="<?php echo Route::_('index.php?option=com_footballmanager&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="player-form" class="form-validate form-vertical">

    <div class="row">
        <div class="col-sm-12 col-md-4">
			<?php echo $this->getForm()->renderField('firstname'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
			<?php echo $this->getForm()->renderField('lastname'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
			<?php echo $this->getForm()->renderField('alias'); ?>
        </div>
    </div>

    <div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general']); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', Text::_('COM_FOOTBALLMANAGER_TAB_BASE_LABEL')); ?>
        <div class="row">
            <div class="col-lg-9">
			    <?php echo $this->getForm()->renderField('about'); ?>
            </div>
            <div class="col-lg-3">
	            <?php echo $this->getForm()->renderField('nickname'); ?>
	            <?php echo $this->getForm()->renderField('image'); ?>
	            <?php echo $this->getForm()->renderField('birthday'); ?>
	            <?php echo $this->getForm()->renderField('weight'); ?>
	            <?php echo $this->getForm()->renderField('height'); ?>
            </div>
        </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'teams', Text::_('COM_FOOTBALLMANAGER_TAB_TEAMS_LABEL')); ?>
        <div class="row">
            <div class="col">
				<?php echo $this->getForm()->renderField('player_teams'); ?>
            </div>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'sponsors', Text::_('COM_FOOTBALLMANAGER_TAB_SPONSORS_LABEL')); ?>
        <div class="row">
            <div class="col-lg-6">
			    <?php echo $this->getForm()->renderField('sponsors'); ?>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <i class="fas fa-handshake fa-lg" style="font-size: 20rem; opacity: .3"></i>
            </div>
        </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_FOOTBALLMANAGER_CONST_PUBLISHING')); ?>
        <div class="row">
            <div class="col-sm-12 col-md-4">
				<?php echo $this->getForm()->renderField('created_at'); ?>
				<?php echo $this->getForm()->renderField('created_by'); ?>
				<?php echo $this->getForm()->renderField('access'); ?>
				<?php echo $this->getForm()->renderField('published'); ?>
				<?php echo $this->getForm()->renderField('catid'); ?>
            </div>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>


		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>


		<?php if (!$isModal && $assoc) : ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS')); ?>
            <fieldset id="fieldset-associations" class="options-form">
                <legend><?php echo Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS') ?></legend>
                <div>
					<?php echo LayoutHelper::render('joomla.edit.associations', $this); ?>
                </div>
            </fieldset>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
		<?php elseif ($isModal && $assoc) : ?>
            <div class="hidden">
                <div class="hidden"><?php echo LayoutHelper::render('joomla.edit.associations', $this); ?></div>
            </div>
		<?php endif; ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>
    <input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
