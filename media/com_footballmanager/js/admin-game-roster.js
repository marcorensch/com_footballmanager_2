// Js File for the Game Roster Edit in the Game Edit View

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
            let playerFound = false;
            const options = players[context].map((player) => {
                playerFound = playerFound || player.players_teams_id.toString() === storedValue.toString();
                const option = {}; //{ value: 'One', label: 'Label One', disabled: true },
                option.label = player.player_number + ' | ' + player.firstname + ' ' + player.lastname;
                option.value = player.players_teams_id; // << this is not the player id but the players_teams id
                option.disabled = false;
                option.selected = storedValue.toString() === player.players_teams_id.toString();
                return option;
            });

            const selectPlayerPlaceholder = {};
            selectPlayerPlaceholder.label = storedValue !== '' ? Joomla.JText._('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_SELECT') : Joomla.JText._('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_INVALID_SELECTION');
            selectPlayerPlaceholder.value = '';
            selectPlayerPlaceholder.disabled = true;
            selectPlayerPlaceholder.selected = !playerFound;

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
            setAvailablePlayersAsOptions(context, playersSelect);
        }
    }

    function addEventListenersToRosterSelects(){
        // Add Event Listener (change) on each select element
        for (let rosterSelect of rosterSelects ) {
            rosterSelect.addEventListener('change', handleRosterSelectChange);
        }
    }

    // When a player is selected in the roster select, fill the player number and position
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

        if(!selectedPlayer.length){
            console.error('No player found for id: ' + event.target.value);
            return;
        }

        selectedPlayer = selectedPlayer[0];

        // Get the Input Elements for this row
        const row = event.target.closest('.subform-repeatable-group');
        row.querySelector('input.player-number').value = selectedPlayer.player_number;
        row.querySelector('joomla-field-fancy-select.player-position').choicesInstance.setChoiceByValue(selectedPlayer.position_id.toString());

    }

    players = await getPlayersForTeams();
    initPlayerSelects();

});