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
use NXD\Component\Footballmanager\Administrator\View\Game\HtmlView;

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

$wa->addInlineScript('//footballmanager game edit');
$wa->addInlineStyle('.switcher label { cursor: pointer; min-width: 2rem; } .nxd-fieldset .control-group .control-label {width:100%;} .nxd-fieldset .controls { min-width: unset;}');

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

?>
<form action="<?php echo Route::_('index.php?option=com_footballmanager&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="player-form" class="form-validate form-vertical">

    <div class="row">
        <div class="col-md-6 col-lg-4">
			<?php echo $this->getForm()->renderField('home_team_id'); ?>
        </div>
        <div class="col-md-6 col-lg-4">
			<?php echo $this->getForm()->renderField('away_team_id'); ?>
        </div>
        <div class="col-md-6 col-lg-4">
			<?php echo $this->getForm()->renderField('alias'); ?>
        </div>
    </div>

    <div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'base']); ?>


	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', Text::_('COM_FOOTBALLMANAGER_TAB_BASE_LABEL')); ?>
        <div class="row">
            <div class="col-lg-4">

                <fieldset class="options-form nxd-fieldset" id="season-setup">
                    <legend><?php echo Text::_("COM_FOOTBALLMANAGER_SEASON_TITLE") ?></legend>
                    <div>
                        <div class="row">
                            <div class="col-12">
							    <?php echo $this->getForm()->renderField('season_id'); ?>
                            </div>
                            <div class="col-12">
							    <?php echo $this->getForm()->renderField('phase_id'); ?>
                            </div>
                            <div class="col-12">
							    <?php echo $this->getForm()->renderField('league_id'); ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-4">
                <fieldset class="options-form nxd-fieldset" id="score-counter">
                    <legend><?php echo Text::_("COM_FOOTBALLMANAGER_SCORE_TITLE") ?></legend>
                    <div>
                        <div class="row">
                            <div class="col-lg-6">
					            <?php echo $this->getForm()->renderField('home_score'); ?>
                            </div>
                            <div class="col-lg-6">
					            <?php echo $this->getForm()->renderField('away_score'); ?>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="options-form nxd-fieldset" id="touchdowns-counter">
                    <legend><?php echo Text::_("COM_FOOTBALLMANAGER_TOUCHDOWNS_TITLE") ?></legend>
                    <div>
                        <div class="row">
                            <div class="col-lg-6">
					            <?php echo $this->getForm()->renderField('home_touchdowns'); ?>
                            </div>
                            <div class="col-lg-6">
					            <?php echo $this->getForm()->renderField('away_touchdowns'); ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="col-lg-4">

                <fieldset class="options-form" id="game-status">
                    <legend><?php echo Text::_("COM_FOOTBALLMANAGER_GAME_TITLE") ?></legend>
                    <div>
                        <div class="row">
                            <div class="col-12">
		                        <?php echo $this->getForm()->renderField('kickoff'); ?>
		                        <?php echo $this->getForm()->renderField('location_id'); ?>
		                        <?php echo $this->getForm()->renderField('matchday'); ?>
		                        <?php echo $this->getForm()->renderField('tickets_link'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><?php echo $this->getForm()->renderField('game_finished'); ?></div>
                            <div class="col-md-4"><?php echo $this->getForm()->renderField('game_postponed'); ?></div>
                            <div class="col-md-4"><?php echo $this->getForm()->renderField('game_canceled'); ?></div>
                        </div>
					    <?php echo $this->getForm()->renderField('new_game_id'); ?>
                    </div>
                </fieldset>
            </div>
        </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>


		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('COM_FOOTBALLMANAGER_TAB_DESCRIPTION_LABEL')); ?>
        <div>
            <div class="row">
                <div class="col-12">
					<?php echo $this->getForm()->renderField('description'); ?>
                </div>
            </div>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>


		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'rosters', Text::_('COM_FOOTBALLMANAGER_TAB_ROSTERS_LABEL')); ?>
        <div class="row">
            <div class="col">

            </div>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'referees', Text::_('COM_FOOTBALLMANAGER_TAB_REFEREES_LABEL')); ?>
        <div class="row">
            <div class="col">
				<?php echo $this->getForm()->renderField('head_referee_id'); ?>
				<?php echo $this->getForm()->renderField('referees'); ?>

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
