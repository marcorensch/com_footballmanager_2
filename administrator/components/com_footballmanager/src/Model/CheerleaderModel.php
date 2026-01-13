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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Item Model for a location.
 *
 * @since  2.0.0
 */
class CheerleaderModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	public $typeAlias = 'com_footballmanager.cheerleader';

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
			'language_id'   => 'batchLanguage',
		];

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \JForm|boolean  A JForm object on success, false on failure
	 *
	 * @throws  \Exception
	 * @since   2.0.0
	 */
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->typeAlias, 'cheerleader', ['control' => 'jform', 'load_data' => $loadData]);

		if (empty($form))
		{
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
		$data = $app->getUserState('com_footballmanager.edit.cheerleader.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('cheerleader.id') == 0)
			{
				$data->catid = $app->getInput()->getInt('catid', $app->getUserState('com_footballmanager.cheerleaders.filter.category_id'));
			}
		}

		$this->preprocessData('com_footballmanager.cheerleader', $data);

		return $data;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Load associated location items

		if (Associations::isEnabled())
		{
			$item->associations = [];
			if ($item->id !== null)
			{
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_cheerleaders', 'com_footballmanager.cheerleader', $item->id, 'id', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

        // Load the linked data (teams, countries, ...)
        if ($item->id > 0) {
            $item->cheerleader_countries = $this->getCountryIds($item->id, 'structured');
        }

		return $item;
	}

	protected function preprocessForm($form, $data, $group = 'content'): void
	{
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * @throws \Exception
	 */
	public function save($data)
	{
		$app   = Factory::getApplication();
		$input = $app->getInput();
		$user  = $app->getIdentity();

		// new element tasks
		if (!isset($data['id']) || (int) $data['id'] === 0)
		{
			$data['created_by'] = $user->id;
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = $this->getTable();

			if ($app->isClient('site'))
			{
				$origTable->load($input->getInt('a_id'));

				if ($origTable->lastname === $data['lastname'])
				{
					/**
					 * If title of article is not changed, set alias to original article alias so that Joomla! will generate
					 * new Title and Alias for the copied article
					 */
					$data['alias'] = $origTable->alias;
				}
				else
				{
					$data['alias'] = '';
				}
			}
			else
			{
				$origTable->load($input->getInt('id'));
			}

			if ($data['lastname'] == $origTable->lastname)
			{
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['lastname']);
				$data['lastname'] = $title;
				$data['alias'] = $alias;
			}
			elseif ($data['alias'] == $origTable->alias)
			{
				$data['alias'] = '';
			}
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['alias'] == null)
		{
			if ($app->get('unicodeslugs') == 1)
			{
				$data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['lastname']);
			}
			else
			{
				$data['alias'] = OutputFilter::stringURLSafe($data['lastname']);
			}

			$table = $this->getTable();

			if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']]))
			{
				$msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
			}

			list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['lastname']);
			$data['alias'] = $alias;

			if (isset($msg))
			{
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
            $this->handleCheerleaderCountriesOnSave($data);
        }

        return $status;

	}

    /* Method to get the country ids of a player */
    private function getCountryIds($id, $format = 'flat'): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('id, country_id, ordering');
        $query->from('#__footballmanager_cheerleaders_countries');
        $query->where('cheerleader_id = ' . (int)$id);
        $query->order('ordering ASC');
        $db->setQuery($query);
        $results = $db->loadAssocList();

        // Create the correct formatted output for subform
        if($results && $format !== 'flat'){
            $formatted = array();
            foreach ($results as $result) {
                $formatted[] = array(
                    'id' => $result['id'],
                    'country_id' => $result['country_id'],
                    'ordering' => $result['ordering']
                );
            }
            return $formatted;
        }

        // Return only country IDs for flat format
        $countryIds = array();
        foreach ($results as $result) {
            $countryIds[] = $result['country_id'];
        }
        return $countryIds;
    }

    private function handleCheerleaderCountriesOnSave(array $data): void
    {
        // Check if we have an ID if not something went wrong
        if (!$data['id']) {
            return;
        }

        $db = $this->getDatabase();
        $cheerleaderId = (int)$data['id'];

        // Get ID's of currently stored player countries data from db
        $countriesFromDb = $this->getCountryIds($cheerleaderId);

        // Save the player country data
        if (!empty($data['cheerleader_countries'])) {
            $countriesFromForm = array();
            $ordering = 0;

            foreach ($data['cheerleader_countries'] as $countryLink) {
                $countryId = (int)$countryLink['country_id'];
                $countriesFromForm[] = $countryId;

                // Update or insert country with ordering
                $query = $db->getQuery(true);

                // Check if country already exists for this player
                $checkQuery = $db->getQuery(true);
                $checkQuery->select('id')
                    ->from($db->quoteName('#__footballmanager_cheerleaders_countries'))
                    ->where($db->quoteName('cheerleader_id') . ' = ' . $cheerleaderId)
                    ->where($db->quoteName('country_id') . ' = ' . $countryId);
                $db->setQuery($checkQuery);
                $existingId = $db->loadResult();

                if ($existingId) {
                    // Update existing record with new ordering
                    $query->update($db->quoteName('#__footballmanager_cheerleaders_countries'))
                        ->set($db->quoteName('ordering') . ' = ' . (int)$ordering)
                        ->where($db->quoteName('id') . ' = ' . (int)$existingId);
                } else {
                    // Insert new record
                    $columns = array('cheerleader_id', 'country_id', 'ordering', 'created');
                    $values = array(
                        $cheerleaderId,
                        $countryId,
                        (int)$ordering,
                        $db->quote((new Date())->toSql())
                    );

                    $query->insert($db->quoteName('#__footballmanager_cheerleaders_countries'))
                        ->columns($db->quoteName($columns))
                        ->values(implode(',', $values));
                }

                $db->setQuery($query);
                $db->execute();

                $ordering++;
            }

            // Delete countries that are no longer in the form
            $toDelete = array_diff($countriesFromDb, $countriesFromForm);
            if (!empty($toDelete)) {
                $query = $db->getQuery(true);
                $query->delete($db->quoteName('#__footballmanager_cheerleaders_countries'))
                    ->where($db->quoteName('cheerleader_id') . ' = ' . $cheerleaderId)
                    ->where($db->quoteName('country_id') . ' IN (' . implode(',', $toDelete) . ')');
                $db->setQuery($query);
                $db->execute();
            }

        } else {
            // Delete all player <--> country relations in the database
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName('cheerleader_id') . ' = ' . $db->quote($cheerleaderId)
            );
            $query->delete($db->quoteName('#__footballmanager_cheerleaders_countries'))->where($conditions);
            $db->setQuery($query);
            $db->execute();
        }
    }
}
