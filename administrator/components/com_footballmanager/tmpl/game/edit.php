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

// Language strings used in JS
Text::script('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_SELECT');

$wa->addInlineScript(<<<JS
document.addEventListener("DOMContentLoaded", async () => {
    
    let players = {};
    
    async function getPlayersForTeams(){
        const homeTeamId = document.querySelector('[name="jform[home_team_id]"]').value;
        const awayTeamId = document.querySelector('[name="jform[away_team_id]"]').value;
        let request = await jQuery.ajax({
            url: "index.php?option=com_footballmanager&controller=players&task=getTeamPlayers&format=json", 
            type: "POST",
            data: {homeTeamId, awayTeamId}, 
            success: function(result){ 
                if(result.status === 200) {
                    return result.data;
                }
                return false;
            }
        });
        return request.data;
    }
    
    const tabSet = document.querySelector('joomla-tab-element#rosters div.row joomla-tab#myTab div');
    tabSet.classList.add('justify-content-center');
    
    // Roster Selection
    // Event Listener on Change for each player select element that is a roster selection
    let rosterSelects = document.querySelectorAll('joomla-field-fancy-select.roster-player-select select');
    
    // Set list of players when a new player (row) got added (on subform-row-add in joomla-field-fancy-select.roster-player-select)
    document.addEventListener('subform-row-add', (event) => {
        if (event.detail.row.querySelector('joomla-field-fancy-select.roster-player-select')) {
            initPlayerSelectUpdates(event.detail.row);
        }
    });
    
    const rosterSubforms = document.querySelectorAll('joomla-field-subform.roster-subform');
    // Add Event Listener (change) on each subform element
    for (let rosterSubform of rosterSubforms ) {
        rosterSubform.addEventListener('dragend', (event) => {
            initPlayerSelectUpdates(event.target);
        });
    }
    
    
    function initPlayerSelectUpdates(row){
        // Update list of roster select inputs
        rosterSelects = document.querySelectorAll('joomla-field-fancy-select.roster-player-select select');
        addEventListenersToRosterSelects();
        
        // Get the context (home or away) by the parent element of class 'team-roster-container'
        const context = row.closest('.team-roster-container').dataset.team;
        const playersSelect = row.querySelector('joomla-field-fancy-select.roster-player-select');
        setAvailablePlayersAsOptions(context, playersSelect);
    }
    
    function setAvailablePlayersAsOptions(context, playersSelect){
        // Set the players as options for the select element
        if(players[context]){
            // First get the current value of the select element
            const storedValue = playersSelect.value;
            // Remove current selection in choicesInstance
            // Remove current selection in select element
            const options = players[context].map((player) => {
                const option = {}; //{ value: 'One', label: 'Label One', disabled: true },
                    option.label = player.player_number + ' | ' + player.firstname + ' ' + player.lastname;
                    option.value = player.players_teams_id; // << this is not the player id but the players_teams id
                    option.disabled = false;
                    option.selected = storedValue.toString() === player.players_teams_id.toString();
                    return option;
            });
            
            const selectPlayerPlaceholder = {};
            selectPlayerPlaceholder.label = Joomla.JText._('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_SELECT');
            selectPlayerPlaceholder.value = '';
            selectPlayerPlaceholder.disabled = true;
            selectPlayerPlaceholder.selected = false;
            
            playersSelect.choicesInstance.setChoices([selectPlayerPlaceholder], 'value', 'label', true );
            playersSelect.choicesInstance.setChoices(options, 'value', 'label', false );
            
        }else{
            console.error('No players found for context: ' + context);
        }
    }
    
    function initPlayerSelects(){
        // Add Event Listener (change) on each select element
        addEventListenersToRosterSelects();
        setPlayerSelectsOptions();
    }
    
    function setPlayerSelectsOptions(){
        for (let rosterSelect of rosterSelects ) {
            // Get the context (home or away) by the parent element of class 'team-roster-container'
            const context = rosterSelect.closest('.team-roster-container').dataset.team;
            const playersSelect = rosterSelect.closest('joomla-field-fancy-select.roster-player-select');
            const value = playersSelect.querySelector('select').value;
            
            setAvailablePlayersAsOptions(context, playersSelect);
        }
    }
    
    function addEventListenersToRosterSelects(){
        // Add Event Listener (change) on each select element
        for (let rosterSelect of rosterSelects ) {
          rosterSelect.addEventListener('change', handleRosterSelectChange);
        }
    }
    
    function handleRosterSelectChange(event){
        // Get the context by parent element of class 'team-roster-container'
        const context = event.target.closest('.team-roster-container').dataset.team;

        if(!players[context]){
            console.error('No players found for context: ' + context);
            return;
        }
        // Find the player object in the players array by the players_teams_id
        let selectedPlayer = players[context].filter(obj => {
          return obj.players_teams_id.toString() === event.target.value.toString();
        });
        
        selectedPlayer = selectedPlayer[0];
        
        // Get the Input Elements for this row
        const row = event.target.closest('.subform-repeatable-group');
        row.querySelector('input.player-number').value = selectedPlayer.player_number;
        row.querySelector('joomla-field-fancy-select.player-position').choicesInstance.setChoiceByValue(selectedPlayer.position_id.toString());

    }
    
    players = await getPlayersForTeams();
    initPlayerSelects();
    
});

// Official Selection
document.addEventListener("DOMContentLoaded", () => {
    const officialsRow = document.getElementById('officials-row');
    const customOfficialsRow = document.getElementById('custom-officials-row');
    // add class to all children of officials-row
    officialsRow.querySelectorAll('.control-group').forEach((el) => {
        el.classList.add('col-lg-4');
    });
    
    // add class to all children of custom-officials-row
    customOfficialsRow.querySelectorAll('#custom-officials-row .subform-wrapper').forEach((el) => {
        el.classList.add('row');
    });
    customOfficialsRow.querySelectorAll('.controls .control-group').forEach((el) => {
        el.classList.add('col-lg-4');
    });
    
});
JS
);
$wa->addInlineStyle(<<<CSS
#rosters joomla-tab-element {
    padding-left:0; padding-right:0; padding-top:10px;
    }
label#jform_home_roster_offense-lbl, label#jform_home_roster_defense-lbl, label#jform_home_roster_special-lbl, label#jform_away_roster_offense-lbl, label#jform_away_roster_defense-lbl, label#jform_away_roster_special-lbl {
    display: none;
    }
CSS
);

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
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'rosters']); ?>


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
