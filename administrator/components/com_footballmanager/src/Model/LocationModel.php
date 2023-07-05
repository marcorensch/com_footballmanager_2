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
class LocationModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	public $typeAlias = 'com_footballmanager.location';

	protected $associationsContext = 'com_footballmanager.item';
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
		$form = $this->loadForm($this->typeAlias, 'location', ['control' => 'jform', 'load_data' => $loadData]);

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
		$data = $app->getUserState($this->option . 'com_footballmanager.edit.location.data', []);

		$data = (count($data)) ? $data : $this->getItem();

		$this->preprocessData('com_footballmanager.location', $data);

		return $data;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		// Load associated location items

		if (Associations::isEnabled())
		{
			$item->associations = [];
			if ($item->id != null)
			{
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_locations', 'com_footballmanager.location', $item->id, 'id', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	protected function preprocessForm($form, $data, $group = 'content')
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
					$field->addAttribute('type', 'modal_partner');
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
			parent::preprocessForm($form, $data, $group);
		}
	}

	public function save($data)
	{
		$app    = Factory::getApplication();
		$input  = $app->getInput();
		$filter = InputFilter::getInstance();

//		if(!array_key_exists('groups',$data) || is_null($data['groups'])){
//			$this->itemGroupIds = array();
//			$data['groups'] = '';
//		}else{
//			$this->itemGroupIds = $data['groups'];
//			$data['groups']	= implode(",", $data['groups']);
//		}

		// set User ID for created & modified
		$user = Factory::getApplication()->getIdentity();
		if(!$data['id']) {
			$data['created_by'] = $user->id;
			$data['created_at'] = Factory::getDate()->toSql();
		}
		$data['modified_by'] = $user->id;

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy') {
			$origTable = $this->getTable();

			if ($app->isClient('site')) {
				$origTable->load($input->getInt('a_id'));

				if ($origTable->title === $data['title']) {
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

			if ($data['title'] == $origTable->title) {
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['title']       = $title;
				$data['alias']       = $alias;
			} elseif ($data['alias'] == $origTable->alias) {
				$data['alias'] = '';
			}
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && (!isset($data['id']) || (int) $data['id'] == 0)) {
			if ($data['alias'] == null) {
				if ($app->get('unicodeslugs') == 1) {
					$data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['title']);
				} else {
					$data['alias'] = OutputFilter::stringURLSafe($data['title']);
				}

				$table = $this->getTable();

				if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']])) {
					$msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
				}

				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
				$data['alias']       = $alias;
				$data['title']       = $title;

				if (isset($msg)) {
					$app->enqueueMessage($msg, 'warning');
				}
			}
		}

		if (parent::save($data))
		{
			$this->itemId = $this->getState($this->getName() . '.id');
//			if ($this->updateItemGroups())
//			{
//				return true;
//			}
			return true;
		}

		return false;
	}

	public function delete(&$pks)
	{
		error_log("delete");
		parent::delete($pks);
	}

//	private function updateItemGroups(): bool
//	{
//		// Get all linked groups for this item from normalized table
//		$this->itemGroupsFromDb = $this->getGroupsForItem();
//
//		// unset all still given/found groups from $itemGroupsFromDb
//		$this->removeLinkedGroupsFromUpdateList();
//
//		// check for leftover groups in db and delete the link
//		if(!$this->deleteItemGroups()){
//			return false;
//		}
//
//		// if this->itemGroups > 0 --> Add new group link to db
//		if(!$this->addNewGroupLinks()){
//			return false;
//		}
//
//		return true;
//	}

//	private function getGroupsForItem(): array
//	{
//		try
//		{
//			$db    = Factory::getDbo();
//			$query = $db->getQuery(true);
//			$query->select($db->quoteName(array('id', 'partner_id', 'group_id')))
//				->from($db->quoteName('#__companypartners_partner_group'))
//				->where($db->quoteName('partner_id') . ' = ' . $db->quote($this->itemId));
//			$db->setQuery($query);
//		}
//		catch (\Exception $e)
//		{
//			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
//
//			return false;
//		}
//
//		return $db->loadObjectList();
//	}
//
//	private function removeLinkedGroupsFromUpdateList()
//	{
//		foreach ($this->itemGroupsFromDb as $key => $group)
//		{
//			if (in_array($group->group_id, $this->itemGroupIds))
//			{
//				unset($this->itemGroupsFromDb[$key]);
//				unset($this->itemGroupIds[array_search($group->group_id, $this->itemGroupIds)]);
//			}
//		}
//	}

//	private function deleteItemGroups(): bool
//	{
//		try
//		{
//			if (count($this->itemGroupsFromDb) > 0)
//
//			{
//				$db    = Factory::getDbo();
//				$query = $db->getQuery(true);
//				foreach ($this->itemGroupsFromDb as $itemGroup)
//				{
//					$query->clear()
//						->delete($db->quoteName('#__companypartners_partner_group'))
//						->where($db->quoteName('id') . ' = ' . $db->quote($itemGroup->id));
//					$db->setQuery($query);
//					$db->execute();
//				}
//			}
//		}
//		catch (\Exception $e)
//		{
//			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
//			error_log($e->getMessage());
//			return false;
//		}
//
//		return true;
//	}
//
//	private function addNewGroupLinks(): bool
//	{
//		try
//		{
//			if (count($this->itemGroupIds) > 0)
//			{
//				$db    = Factory::getDbo();
//				$query = $db->getQuery(true);
//				foreach ($this->itemGroupIds as $itemGroupId)
//				{
//					if((int) $itemGroupId === 0) continue;
//					$columns = array('partner_id', 'group_id');
//					$values  = array($db->quote($this->itemId), $db->quote((int)$itemGroupId));
//					$query->clear()
//						->insert($db->quoteName('#__companypartners_partner_group'))
//						->columns($db->quoteName($columns))
//						->values(implode(',', $values));
//					$db->setQuery($query);
//					$db->execute();
//				}
//			}
//		}
//		catch (Exception $e)
//		{
//			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
//			error_log($e->getMessage());
//			return false;
//		}
//
//		return true;
//	}
}
