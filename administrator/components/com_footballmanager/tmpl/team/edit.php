<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use NXD\Component\Footballmanager\Administrator\View\Team\HtmlView;

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
      method="post" name="adminForm" id="team-form" class="form-validate form-vertical">

	<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'general']); ?>
		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_FOOTBALLMANAGER_TEAM_TITLE')); ?>
        <div class="row">
            <div class="col-lg-9">
		        <?php echo $this->getForm()->renderField('introtext'); ?>
		        <?php echo $this->getForm()->renderField('description'); ?>
            </div>
            <div class="col-lg-3">
                <fieldset id="basic-info" class="options-form">
                    <legend><?php echo Text::_('COM_FOOTBALLMANAGER_BASIC'); ?></legend>
                    <div>
	                    <?php echo $this->getForm()->renderField('context'); ?>
	                    <?php echo $this->getForm()->renderField('year_established'); ?>
	                    <?php echo $this->getForm()->renderField('my_team'); ?>
	                    <?php echo $this->getForm()->renderField('related_team_id'); ?>
	                    <?php echo $this->getForm()->renderField('shortname'); ?>
	                    <?php echo $this->getForm()->renderField('shortcode'); ?>

	                    <?php echo $this->getForm()->renderField('color'); ?>
	                    <?php echo $this->getForm()->renderField('secondary_color'); ?>


	                    <?php echo $this->getForm()->renderField('location_id'); ?>
	                    <?php echo $this->getForm()->renderField('catid'); ?>
	                    <?php echo $this->getForm()->renderField('id'); ?>
                    </div>
                </fieldset>

            </div>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_FOOTBALLMANAGER_IMAGES')); ?>

        <div class="row">
            <div class="col-lg-4">
	            <?php echo $this->getForm()->renderField('logo'); ?>
            </div>
            <div class="col-lg-4">
		        <?php echo $this->getForm()->renderField('inverted_logo'); ?>
            </div>
            <div class="col-lg-4">
	            <?php echo $this->getForm()->renderField('image'); ?>
            </div>
        </div>

	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_FOOTBALLMANAGER_CONTACT')); ?>

        <div class="row">
            <div class="col-lg-6">
                <h3>GeoData</h3>
	            <?php echo $this->getForm()->renderField('street'); ?>
	            <?php echo $this->getForm()->renderField('zip'); ?>
	            <?php echo $this->getForm()->renderField('city'); ?>
            </div>
            <div class="col-lg-6">
                <h3>Contact</h3>
	            <?php echo $this->getForm()->renderField('website'); ?>
	            <?php echo $this->getForm()->renderField('email'); ?>
	            <?php echo $this->getForm()->renderField('phone'); ?>
            </div>
        </div>

		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_FOOTBALLMANAGER_SPONSORS')); ?>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <?php echo $this->getForm()->renderField('sponsors'); ?>
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
				<?php echo $this->getForm()->renderField('language'); ?>
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
