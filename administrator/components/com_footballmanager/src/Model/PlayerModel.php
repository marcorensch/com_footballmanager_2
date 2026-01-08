<?php
/**
 * @package     com_footballmanager
 *
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\Input\Json;
use NXD\Component\Footballmanager\Administrator\Helper\FootballmanagerHelper;

/**
 * Item Model for a location.
 *
 * @since  2.0.0
 */
class PlayerModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var    string
     * @since  2.0.0
     */
    public $typeAlias = 'com_footballmanager.player';

    protected $associationsContext = 'com_footballmanager.player';
    private $itemId = 0;

    protected $batch_copymove = 'category_id';

    /**
     * Allowed batch commands
     *
     * @var array
     */
    protected $batch_commands
        = [
            'assetgroup_id' => 'batchAccess',
            'language_id' => 'batchLanguage',
        ];

    /**
     * Method to get the row form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  \JForm|boolean  A JForm object on success, false on failure
     *
     * @throws  \Exception
     * @since   2.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->typeAlias, 'player', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     *
     * @throws \Exception
     * @since   2.0.0
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();

        // Check the session for previously entered form data.
        $data = $app->getUserState('com_footballmanager.edit.player.data', []);

        if (empty($data)) {
            $data = $this->getItem();
            if ($this->getState('player.id') == 0) {
                $data->catid = $app->getInput()->getInt('catid', $app->getUserState('com_footballmanager.players.filter.category_id'));
            }
        }

        $this->preprocessData('com_footballmanager.player', $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        // Load associated location items

        if (Associations::isEnabled()) {
            $item->associations = [];
            if ($item->id !== null) {
                $associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_players', 'com_footballmanager.player', $item->id, 'id', null);

                foreach ($associations as $tag => $association) {
                    $item->associations[$tag] = $association->id;
                }
            }
        }

        // Load the linked data (teams, countries, ...)
        if ($item->id > 0) {
            $item->player_teams = $this->getTeamLinks($item->id);
            $item->player_countries = $this->getCountryIds($item->id);
        }

        return $item;
    }

    protected function getTeamLinks($id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__footballmanager_players_teams');
        $query->where('player_id = ' . (int)$id);
        $query->order('ordering ASC');
        $db->setQuery($query);
        $teams = $db->loadAssocList();
        return $teams;
    }

    protected function preprocessForm($form, $data, $group = 'content'): void
    {
        if (Associations::isEnabled()) {
            $languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

            if (count($languages) > 1) {

                $addform = new \SimpleXMLElement('<form />');
                $fields = $addform->addChild('fields');
                $fields->addAttribute('name', 'associations');

                $fieldset = $fields->addChild('fieldset');
                $fieldset->addAttribute('name', 'item_associations');

                foreach ($languages as $language) {
                    $field = $fieldset->addChild('field');
                    $field->addAttribute('name', $language->lang_code);
                    $field->addAttribute('type', 'modal_location');
                    $field->addAttribute('language', $language->lang_code);
                    $field->addAttribute('label', $language->title);
                    $field->addAttribute('translate_label', 'false');
                    $field->addAttribute('select', 'true');
                    $field->addAttribute('new', 'true');
                    $field->addAttribute('edit', 'true');
                    $field->addAttribute('clear', 'true');
                }

                $form->load($addform, false);
            }
        }

        parent::preprocessForm($form, $data, $group);
    }

    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $user = $app->getIdentity();

        // new element tasks
        if (!isset($data['id']) || (int)$data['id'] === 0) {
            $data['created_by'] = $user->id;
        }

        // sponsors
        $data['sponsors'] = json_encode($data['sponsors']);

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy') {
            $origTable = $this->getTable();

            if ($app->isClient('site')) {
                $origTable->load($input->getInt('a_id'));

                if ($origTable->lastname === $data['lastname']) {
                    /**
                     * If title of article is not changed, set alias to original article alias so that Joomla! will generate
                     * new Title and Alias for the copied article
                     */
                    $data['alias'] = $origTable->alias;
                } else {
                    $data['alias'] = '';
                }
            } else {
                $origTable->load($input->getInt('id'));
            }

            if ($data['lastname'] == $origTable->lastname) {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['lastname']);
                $data['lastname'] = $title;
                $data['alias'] = $alias;
            } elseif ($data['alias'] == $origTable->alias) {
                $data['alias'] = '';
            }
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['alias'] == null) {
            if ($app->get('unicodeslugs') == 1) {
                $data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['lastname']);
            } else {
                $data['alias'] = OutputFilter::stringURLSafe($data['lastname']);
            }

            $table = $this->getTable();

            if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']])) {
                $msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
            }

            list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['lastname']);
            $data['alias'] = $alias;

            if (isset($msg)) {
                $app->enqueueMessage($msg, 'warning');
            }
        }

        $status = parent::save($data);

        if ($status) {
            if(!$data['id']){
                $id = $this->getState($this->getName() . '.id');
                if($id){
                    $data['id'] = $id;
                }
            }
            $this->handlePlayerTeamsOnSave($data);
            $this->handlePlayerCountriesOnSave($data);
        }

        return $status;
    }

    protected function handlePlayerTeamsOnSave($data): void
    {
        // Check if we have an ID if not we have added a new player get the id by alias
        if (!$data['id']) {
            $db = $this->getDatabase();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__footballmanager_players');
            $query->where('alias = ' . $db->quote($data['alias']));
            $db->setQuery($query);
            $data['id'] = $db->loadResult();
        }

        // Get ID's of currently stored player teams data from db
        $playerTeamIds = $this->getTeamLinkIds($data['id']);

        // Save the player teams data
        if ($data['player_teams']) {
            $teamLinks = $data['player_teams'];
            $db = $this->getDatabase();

            foreach ($teamLinks as $teamLinkData) {
                // remove from array if exists (leftovers will be deleted)
                if ($teamLinkData['id'] > 0 && in_array($teamLinkData['id'], $playerTeamIds)) {
                    unset($playerTeamIds[array_search($teamLinkData['id'], $playerTeamIds)]);
                }

                // Cleanup empty fields
                if (!$teamLinkData['team_id']) $teamLinkData['team_id'] = null;
                if (!$teamLinkData['player_number']) $teamLinkData['player_number'] = "0";
                if (!$teamLinkData['position_id']) $teamLinkData['position_id'] = null;
                if (!$teamLinkData['since']) $teamLinkData['since'] = null;
                if (!$teamLinkData['until']) $teamLinkData['until'] = null;
                if (!$teamLinkData['league_id']) $teamLinkData['league_id'] = null;

                if (!$teamLinkData['id'] &&
                    !$teamLinkData['team_id'] &&
                    !$teamLinkData['player_number'] &&
                    !$teamLinkData['image'] &&
                    !$teamLinkData['since'] &&
                    !$teamLinkData['until'] &&
                    !$teamLinkData['position_id'] &&
                    !$teamLinkData['league_id']) {
                    continue;
                }

                $query = $db->getQuery(true);

                if ($teamLinkData['id'] > 0) {

                    foreach (array('ordering', 'image', 'player_number', 'since', 'until', 'team_id', 'league_id', 'position_id') as $key) {
                        if ($teamLinkData[$key] === null) {
                            $fields[] = $db->quoteName($key) . ' = NULL';
                        } else {
                            $fields[] = $db->quoteName($key) . ' = ' . $db->quote($teamLinkData[$key]);
                        }
                    }

                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = ' . $db->quote($teamLinkData['id'])
                    );

                    $query->update($db->quoteName('#__footballmanager_players_teams'))->set($fields)->where($conditions);

                    $db->setQuery($query);

                    $db->execute();
                } else {
                    $teamLinkDataObj = (object)$teamLinkData;
                    $teamLinkDataObj->player_id = $data['id'];
                    $result = $this->getDatabase()->insertObject('#__footballmanager_players_teams', $teamLinkDataObj);
                }
            }

            // Delete leftover player teams data
            foreach ($playerTeamIds as $playerTeamId) {
                $query = $db->getQuery(true);
                $conditions = array(
                    $db->quoteName('id') . ' = ' . $db->quote($playerTeamId)
                );
                $query->delete($db->quoteName('#__footballmanager_players_teams'))->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        } else {
            // Delete all player teams relations in database
            $db = $this->getDatabase();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('player_id') . ' = ' . $db->quote($data['id'])
            );
            $query->delete($db->quoteName('#__footballmanager_players_teams'))->where($conditions);
            $db->setQuery($query);
            $db->execute();
        }
    }

    protected function getTeamLinkIds($playerId): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__footballmanager_players_teams');
        $query->where('player_id = ' . $playerId);
        $db->setQuery($query);
        $teamLinks = $db->loadObjectList();
        $teamLinkIds = array();
        foreach ($teamLinks as $teamLink) {
            $teamLinkIds[] = $teamLink->id;
        }
        return $teamLinkIds;

    }

    /* Method to get the country ids of a player */
    private function getCountryIds($id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('country_id');
        $query->from('#__footballmanager_players_countries');
        $query->where('player_id = ' . (int)$id);
        $db->setQuery($query);
        $countryIds = $db->loadColumn();
        return $countryIds;
    }

    private function handlePlayerCountriesOnSave(array $data): void
    {
        // Check if we have an ID if not something went wrong
        if (!$data['id']) {
           return;
        }

        $db = $this->getDatabase();
        $playerId = (int)$data['id'];

        // Get ID's of currently stored player countries data from db
        $existingCountryIds = $this->getCountryIds($playerId);


        // Save the player country data
        if (!empty($data['player_countries'])) {
            $countryLinks = $data['player_countries'];

            // Find countries to delete (in DB but not in new data)
            $toDelete = array_diff($existingCountryIds, $countryLinks);

            // Find countries to add (in new data but not in DB)
            $toAdd = array_diff($countryLinks, $existingCountryIds);

            // Delete removed countries
            if (!empty($toDelete)) {
                $query = $db->getQuery(true);
                $query->delete($db->quoteName('#__footballmanager_players_countries'))
                    ->where($db->quoteName('player_id') . ' = ' . $playerId)
                    ->where($db->quoteName('country_id') . ' IN (' . implode(',', $toDelete) . ')');
                $db->setQuery($query);
                $db->execute();
            }

            // Add new countries
            if (!empty($toAdd)) {
                foreach ($toAdd as $countryId) {
                    $query = $db->getQuery(true);
                    $columns = array('player_id', 'country_id', 'is_primary', 'created');

                    // Set is_primary to 1 for the first country, 0 for others
                    $isPrimary = 1;

                    $values = array(
                        $playerId,
                        (int)$countryId,
                        $isPrimary,
                        $db->quote((new Date())->toSql())
                    );

                    $query->insert($db->quoteName('#__footballmanager_players_countries'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));

                    $db->setQuery($query);
                    $db->execute();
                }
            }

        } else {
            // Delete all player <--> country relations in the database
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('player_id') . ' = ' . $db->quote($playerId)
            );
            $query->delete($db->quoteName('#__footballmanager_players_countries'))->where($conditions);
            $db->setQuery($query);
            $db->execute();
        }
    }
}
