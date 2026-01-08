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

/**
 * Item Model for a location.
 *
 * @since  2.0.0
 */
class OfficialModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	public $typeAlias = 'com_footballmanager.official';

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
		$form = $this->loadForm($this->typeAlias, 'official', ['control' => 'jform', 'load_data' => $loadData]);

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
		$data = $app->getUserState('com_footballmanager.edit.official.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('official.id') == 0)
			{
				$data->catid = $app->getInput()->getInt('catid', $app->getUserState('com_footballmanager.officials.filter.category_id'));
			}
		}

		$this->preprocessData('com_footballmanager.official', $data);

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
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_officials', 'com_footballmanager.official', $item->id, 'id', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

        // Load the linked data (teams, countries, ...)
        if ($item->id > 0) {
            $item->official_countries = $this->getCountryIds($item->id);
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

		if(!$data['alias']){
			$data['alias'] = OutputFilter::stringURLSafe($data['lastname']);
		}

        $status = parent::save($data);

        if ($status) {
            if(!$data['id']){
                $id = $this->getState($this->getName() . '.id');
                if($id){
                    $data['id'] = $id;
                }
            }
            $this->handleOfficialCountriesOnSave($data);
        }

        return $status;

	}

    /* Method to get the country ids of an official */
    private function getCountryIds($id)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('country_id');
        $query->from('#__footballmanager_officials_countries');
        $query->where('official_id = ' . (int)$id);
        $db->setQuery($query);
        $countryIds = $db->loadColumn();
        return $countryIds;
    }

    private function handleOfficialCountriesOnSave(array $data): void
    {
        // Check if we have an ID if not something went wrong
        if (!$data['id']) {
            return;
        }

        $db = $this->getDatabase();
        $officialId = (int)$data['id'];

        // Get ID's of currently stored player countries data from db
        $existingCountryIds = $this->getCountryIds($officialId);

        // Save the player country data
        if (!empty($data['official_countries'])) {
            $countryLinks = $data['official_countries'];

            // Find countries to delete (in DB but not in new data)
            $toDelete = array_diff($existingCountryIds, $countryLinks);

            // Find countries to add (in new data but not in DB)
            $toAdd = array_diff($countryLinks, $existingCountryIds);

            // Delete removed countries
            if (!empty($toDelete)) {
                $query = $db->getQuery(true);
                $query->delete($db->quoteName('#__footballmanager_officials_countries'))
                    ->where($db->quoteName('official_id') . ' = ' . $officialId)
                    ->where($db->quoteName('country_id') . ' IN (' . implode(',', $toDelete) . ')');
                $db->setQuery($query);
                $db->execute();
            }

            // Add new countries
            if (!empty($toAdd)) {
                foreach ($toAdd as $countryId) {
                    error_log("adding country: $countryId to official: $officialId");
                    $query = $db->getQuery(true);
                    $columns = array('official_id', 'country_id', 'is_primary', 'created');

                    // Set is_primary to 1 for the first country, 0 for others
                    $isPrimary = 1;

                    $values = array(
                        $officialId,
                        (int)$countryId,
                        $isPrimary,
                        $db->quote((new Date())->toSql())
                    );

                    $query->insert($db->quoteName('#__footballmanager_officials_countries'))
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
                $db->quoteName('official_id') . ' = ' . $db->quote($officialId)
            );
            $query->delete($db->quoteName('#__footballmanager_officials_countries'))->where($conditions);
            $db->setQuery($query);
            $db->execute();
        }
    }

}
