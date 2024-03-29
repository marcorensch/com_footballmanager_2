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
use Joomla\Database\DatabaseInterface;
use Joomla\Input\Json;
use SimpleXMLElement;

/**
 * Item Model for a location.
 *
 * @since  __BUMP_VERSION__
 */
class GameModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	public $typeAlias = 'com_footballmanager.game';

	protected $associationsContext = 'com_footballmanager.game';
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
		$form = $this->loadForm($this->typeAlias, 'game', ['control' => 'jform', 'load_data' => $loadData]);

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
	protected function loadFormData(): mixed
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_footballmanager.edit.game.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('game.id') == 0)
			{
				$data->set('catid', $app->getInput()->get('catid', $app->getUserState('com_footballmanager.game.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData($this->typeAlias, $data);

		return $data;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		return $item;
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

		// handle array data
		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				$data[$key] = json_encode($value);
			}
		}

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = $this->getTable();

			if ($app->isClient('site'))
			{
				$origTable->load($input->getInt('a_id'));

				if ($origTable->home_team_id === $data['home_team_id'] && $origTable->away_team_id === $data['away_team_id'] && $origTable->kickoff === $data['kickoff'])
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

			if ($origTable->home_team_id === $data['home_team_id'] && $origTable->away_team_id === $data['away_team_id'] && $origTable->kickoff === $data['kickoff'])
			{
				$tempTitle = $data['home_team_id'] . ' - ' . $data['away_team_id'] . ' ' . $data['kickoff'];
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $tempTitle);
				$data['title'] = $title;
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
				$data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['home_team_id'] . ' - ' . $data['away_team_id'] . ' ' . $data['kickoff']);
			}
			else
			{
				$data['alias'] = OutputFilter::stringURLSafe($data['home_team_id'] . ' - ' . $data['away_team_id'] . ' ' . $data['kickoff']);
			}

			$table = $this->getTable();

			if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']]))
			{
				$msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
			}

			list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['home_team_id'] . ' - ' . $data['away_team_id'] . ' ' . $data['kickoff']);
			$data['alias'] = $alias;

			if (isset($msg))
			{
				$app->enqueueMessage($msg, 'warning');
			}
		}

		$status = parent::save($data);

		if($status){
			$this->handleScoreData($data);
		}

		return $status;
	}

	protected function handleScoreData($data): void
	{
		error_log('handleScoreData');
		// Check if we have an ID if not we have added a new coach get the id by alias
		if(!$data['id']){
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__footballmanager_games');
			$query->where('alias = ' . $db->quote($data['alias']));
			$db->setQuery($query);
			$data['id'] = $db->loadResult();
		}

		error_log('handleScoreDataForID: ' . $data['id']);

//		// Get ID's of currently stored coaches teams data from db
//		$playerTeamIds = $this->getTeamLinkIds($data['id']);
//
//		// Save the player teams data
//		if($data['player_teams'])
//		{
//			$teamLinks = $data['player_teams'];
//			$db = $this->getDatabase();
//
//			foreach($teamLinks as $teamLinkData)
//			{
//				// remove from array if exists (leftovers will be deleted)
//				if($teamLinkData['id'] > 0 && in_array($teamLinkData['id'], $playerTeamIds)){
//					unset($playerTeamIds[array_search($teamLinkData['id'], $playerTeamIds)]);
//				}
//
//				// Cleanup empty fields
//				if(!$teamLinkData['team_id']) $teamLinkData['team_id'] = null;
//				if(!$teamLinkData['player_number']) $teamLinkData['player_number'] = null;
//				if(!$teamLinkData['position_id']) $teamLinkData['position_id'] = null;
//				if(!$teamLinkData['since']) $teamLinkData['since'] = null;
//				if(!$teamLinkData['until']) $teamLinkData['until'] = null;
//
//				if(!$teamLinkData['id'] && !$teamLinkData['team_id'] && !$teamLinkData['player_number'] && !$teamLinkData['image'] && !$teamLinkData['since'] && !$teamLinkData['until'] && !$teamLinkData['position_id']){
//					continue;
//				}
//
//				$query = $db->getQuery(true);
//
//				if($teamLinkData['id'] > 0){
//					// Fields to update.
//					// Below fields needs a special handling
//					$fields = array(
//						$db->quoteName('position_id') . ' = ' . $db->quote(json_encode($teamLinkData['position_id'])),
//						$db->quoteName('ordering') . ' = ' . $db->quote($teamLinkData['ordering']),
//					);
//
//					foreach(array('image','player_number','since', 'until', 'team_id') as $key){
//						if(!$teamLinkData[$key]){
//							$fields[] = $db->quoteName($key) . ' = NULL';
//						}else{
//							$fields[] = $db->quoteName($key) . ' = ' . $db->quote($teamLinkData[$key]);
//						}
//					}
//
//					// Conditions for which records should be updated.
//					$conditions = array(
//						$db->quoteName('id') . ' = ' . $db->quote($teamLinkData['id'])
//					);
//
//					$query->update($db->quoteName('#__footballmanager_players_teams'))->set($fields)->where($conditions);
//
//					$db->setQuery($query);
//
//					$result = $db->execute();
//				}else{
//					$teamLinkDataObj = (object) $teamLinkData;
//					$teamLinkDataObj->player_id = $data['id'];
//					$result        = $this->getDatabase()->insertObject('#__footballmanager_players_teams', $teamLinkDataObj);
//				}
//			}
//
//			// Delete leftover player teams data
//			foreach($playerTeamIds as $coachTeamId){
//				$query = $db->getQuery(true);
//				$conditions = array(
//					$db->quoteName('id') . ' = ' . $db->quote($coachTeamId)
//				);
//				$query->delete($db->quoteName('#__footballmanager_players_teams'))->where($conditions);
//				$db->setQuery($query);
//				$db->execute();
//			}
//		}
	}
}
