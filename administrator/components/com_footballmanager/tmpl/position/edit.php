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
use NXD\Component\Footballmanager\Administrator\View\Location\HtmlView;

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

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

?>
<form action="<?php echo Route::_('index.php?option=com_footballmanager&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="league-form" class="form-validate form-vertical">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general']); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_FOOTBALLMANAGER_TAB_DETAILS_TITLE')); ?>
        <div class="row">
            <div class="col-sm-12 col-md-3">
			    <?php echo $this->getForm()->renderField('shortname'); ?>
            </div>
            <div class="col-sm-12 col-md-9">
			    <?php echo $this->getForm()->renderField('learnmore_link'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
	            <?php echo $this->getForm()->renderField('description'); ?>
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
