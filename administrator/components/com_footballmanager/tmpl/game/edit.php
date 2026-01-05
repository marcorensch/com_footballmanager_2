<?php
/**
 * @package     com_footballmanager
 * 
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

// Language strings used in JS
Text::script('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_SELECT');
Text::script('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_INVALID_SELECTION');

/** @var HtmlView $this */

$app   = Factory::getApplication();
$input = $app->input;

$assoc = Associations::isEnabled();

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$isModal = $input->get('layout') === 'modal';

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate')
    ->useScript('jquery')
    ->useScript('com_footballmanager.admin-game-roster-js')
    ->useScript('com_footballmanager.admin-game-officials-js')
    ->useStyle('com_footballmanager.admin-game-officials-css');

$layout = 'edit';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

?>
<form action="<?php echo Route::_('index.php?option=com_footballmanager&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="player-form" class="form-validate form-vertical">

    <div class="row">
        <div class="col-md-6 col-lg-3">
			<?php echo $this->getForm()->renderField('home_team_id'); ?>
        </div>
        <div class="col-md-6 col-lg-3">
			<?php echo $this->getForm()->renderField('away_team_id'); ?>
        </div>
        <div class="col-md-6 col-lg-3">
	        <?php echo $this->getForm()->renderField('location_id'); ?>

        </div>
        <div class="col-md-6 col-lg-3">
		    <?php echo $this->getForm()->renderField('kickoff'); ?>

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
								<?php echo $this->getForm()->renderField('matchday'); ?>
								<?php echo $this->getForm()->renderField('tickets_link'); ?>
								<?php echo $this->getForm()->renderField('id'); ?>
								<?php echo $this->getForm()->renderField('support_games'); ?>
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
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'roster-home', 'class' => 'justify-content-center']); ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-home', Text::_('COM_FOOTBALLMANAGER_TAB_HOME_LABEL')); ?>
            <div class="team-roster-container" data-team="home">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'roster-home-offense', 'orientation' => 'vertical']); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-home-offense', Text::_('COM_FOOTBALLMANAGER_TAB_OFFENSE_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('home_roster_offense'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-home-defense', Text::_('COM_FOOTBALLMANAGER_TAB_DEFENSE_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('home_roster_defense'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-home-special', Text::_('COM_FOOTBALLMANAGER_TAB_SPECIAL_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('home_roster_special'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-away', Text::_('COM_FOOTBALLMANAGER_TAB_AWAY_LABEL')); ?>
            <div class="team-roster-container" data-team="away">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'roster-away-offense', 'orientation' => 'vertical']); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-away-offense', Text::_('COM_FOOTBALLMANAGER_TAB_OFFENSE_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('away_roster_offense'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-away-defense', Text::_('COM_FOOTBALLMANAGER_TAB_DEFENSE_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('away_roster_defense'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
                    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'roster-away-special', Text::_('COM_FOOTBALLMANAGER_TAB_SPECIAL_LABEL')); ?>
                    <div class="row">
                        <div class="col-lg-12 ps-5">
                            <?php echo $this->getForm()->renderField('away_roster_special'); ?>
                        </div>
                    </div>
                    <?php echo HTMLHelper::_('uitab.endTab'); ?>
			    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'officials', Text::_('COM_FOOTBALLMANAGER_TAB_OFFICIALS_LABEL')); ?>
        <div id="officials-row" class="row">
			<?php echo $this->getForm()->renderField('head_referee_id'); ?>
        </div>
        <div id="custom-officials-row">
			<?php echo $this->getForm()->renderField('officials'); ?>
        </div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'sponsors', Text::_('COM_FOOTBALLMANAGER_TAB_SPONSORS_LABEL')); ?>
        <div class="row">
            <div class="col-lg-6">
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
				<?php echo $this->getForm()->renderField('catid'); ?>
				<?php echo $this->getForm()->renderField('alias'); ?>
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
