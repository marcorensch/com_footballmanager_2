<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Model;

\defined('_JEXEC') or die;

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
 * @since  __BUMP_VERSION__
 */
class CoachModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	public $typeAlias = 'com_footballmanager.coach';

	protected $associationsContext = 'com_footballmanager.coach';
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
	 * @since   __BUMP_VERSION__
	 */
	public function getForm($data = [], $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm($this->typeAlias, 'coach', ['control' => 'jform', 'load_data' => $loadData]);

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
	 * @since   __BUMP_VERSION__
	 */
	protected function loadFormData()
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState($this->option . 'com_footballmanager.edit.coach.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('coach.id') == 0)
			{
				$data->set('catid', $app->getInput()->getInt('catid', $app->getUserState('com_footballmanager.coaches.filter.category_id')));
			}
		}

		$this->preprocessData('com_footballmanager.coach', $data);

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
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_coaches', 'com_footballmanager.coach', $item->id, 'id', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		// Load the "linked teams" data
		if($item->id > 0) $item->coach_teams = $this->getTeamLinks($item->id);

		return $item;
	}

	protected function getTeamLinks($id)
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__footballmanager_coaches_teams');
		$query->where('coach_id = ' . (int) $id);
		$query->order('ordering ASC');
		$db->setQuery($query);
		$teams = $db->loadAssocList();
		return $teams;
	}

	protected function preprocessForm($form, $data, $group = 'content'): void
	{
		if (Associations::isEnabled())
		{
			$languages = LanguageHelper::getContentLanguages(false, true, null, 'ordering', 'asc');

			if (count($languages) > 1)
			{

				$addform = new \SimpleXMLElement('<form />');
				$fields  = $addform->addChild('fields');
				$fields->addAttribute('name', 'associations');

				$fieldset = $fields->addChild('fieldset');
				$fieldset->addAttribute('name', 'item_associations');

				foreach ($languages as $language)
				{
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

		if($status)
		{
			$this->handleCoachTeamsOnSave($data);
		}

		return $status;

	}

	protected function handleCoachTeamsOnSave($data): void
	{
		// Check if we have an ID if not we have added a new coach get the id by alias
		if(!$data['id']){
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__footballmanager_coaches');
			$query->where('alias = ' . $db->quote($data['alias']));
			$db->setQuery($query);
			$data['id'] = $db->loadResult();
		}
		// Get ID's of currently stored coaches teams data from db
		$coachTeamIds = $this->getTeamLinkIds($data['id']);

		// Save the coach teams data
		if($data['coach_teams'])
		{
			$teamLinks = $data['coach_teams'];
			$db = $this->getDatabase();

			foreach($teamLinks as $teamLinkData)
			{
				// remove from array if exists (leftovers will be deleted)
				if($teamLinkData['id'] > 0 && in_array($teamLinkData['id'], $coachTeamIds)){
					unset($coachTeamIds[array_search($teamLinkData['id'], $coachTeamIds)]);
				}

				// Cleanup empty fields
				if(!$teamLinkData['team_id']) $teamLinkData['team_id'] = null;
				if(!$teamLinkData['position_id']) $teamLinkData['position_id'] = null;
				if(!$teamLinkData['since']) $teamLinkData['since'] = null;
				if(!$teamLinkData['until']) $teamLinkData['until'] = null;

				if(!$teamLinkData['id'] && !$teamLinkData['team_id'] && !$teamLinkData['image'] && !$teamLinkData['since'] && !$teamLinkData['until'] && !$teamLinkData['position_id']){
					continue;
				}

				$query = $db->getQuery(true);

				if($teamLinkData['id'] > 0){
					// Fields to update.
					$fields = array(
						$db->quoteName('image') . ' = ' . $db->quote($teamLinkData['image']),
						$db->quoteName('ordering') . ' = ' . $db->quote($teamLinkData['ordering']),
					);

					foreach(array('since', 'until', 'position_id', 'team_id') as $key){
						if(!$teamLinkData[$key]){
							$fields[] = $db->quoteName($key) . ' = NULL';
						}else{
							$fields[] = $db->quoteName($key) . ' = ' . $db->quote($teamLinkData[$key]);
						}
					}

					// Conditions for which records should be updated.
					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($teamLinkData['id'])
					);

					$query->update($db->quoteName('#__footballmanager_coaches_teams'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$result = $db->execute();
				}else{
					$teamLinkDataObj = (object) $teamLinkData;
					$teamLinkDataObj->coach_id = $data['id'];
					$result        = $this->getDatabase()->insertObject('#__footballmanager_coaches_teams', $teamLinkDataObj);
				}
			}

			// Delete leftover coach teams data
			foreach($coachTeamIds as $coachTeamId){
				$query = $db->getQuery(true);
				$conditions = array(
					$db->quoteName('id') . ' = ' . $db->quote($coachTeamId)
				);
				$query->delete($db->quoteName('#__footballmanager_coaches_teams'))->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	protected function getTeamLinkIds($coachId): array
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__footballmanager_coaches_teams');
		$query->where('coach_id = ' . $coachId);
		$db->setQuery($query);
		$teamLinks = $db->loadObjectList();
		$teamLinkIds = array();
		foreach($teamLinks as $teamLink){
			$teamLinkIds[] = $teamLink->id;
		}
		return $teamLinkIds;

	}
}
