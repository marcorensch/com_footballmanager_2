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

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\String\StringHelper;

/**
 * Item Model for a location.
 *
 * @since  2.0.0
 */
class LeagueModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	public $typeAlias = 'com_footballmanager.league';

	protected $associationsContext = 'com_footballmanager.league';
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
		$form = $this->loadForm($this->typeAlias, 'league', ['control' => 'jform', 'load_data' => $loadData]);

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
		$data = $app->getUserState('com_footballmanager.edit.league.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('league.id') == 0)
			{
				$data->catid = $app->getInput()->getInt('catid', $app->getUserState('com_footballmanager.leagues.filter.category_id'));
			}
		}

		$this->preprocessData('com_footballmanager.league', $data);

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
				$associations = Associations::getAssociations('com_footballmanager', '#__footballmanager_leagues', 'com_footballmanager.league', $item->id, 'id', null);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
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

	protected function generateNewTitleWithoutCat($alias, $title)
	{
		// Alter the title & alias
		$table      = $this->getTable();
		$aliasField = $table->getColumnAlias('alias');
		$titleField = $table->getColumnAlias('title');

		while ($table->load([$aliasField => $alias])) {
			if ($title === $table->$titleField) {
				$title = StringHelper::increment($title);
			}

			$alias = StringHelper::increment($alias, 'dash');
		}

		return [$title, $alias];
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

				if ($origTable->title === $data['title'])
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

			if ($data['title'] == $origTable->title)
			{
				list($title, $alias) = $this->generateNewTitleWithoutCat($data['alias'], $data['title']);
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
				$data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['title']);
			}
			else
			{
				$data['alias'] = OutputFilter::stringURLSafe($data['title']);
			}

			$table = $this->getTable();

			if ($table->load(['alias' => $data['alias']]))
			{
				$msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
			}

			list($title, $alias) = $this->generateNewTitleWithoutCat($data['alias'], $data['title']);
			$data['alias'] = $alias;

			if (isset($msg))
			{
				$app->enqueueMessage($msg, 'warning');
			}
		}

		return parent::save($data);
	}
}
