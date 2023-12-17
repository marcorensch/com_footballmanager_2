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

	protected $associationsContext = 'com_footballmanager.location';
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
	protected function loadFormData(): mixed
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState($this->option . 'com_footballmanager.edit.location.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
			if ($this->getState('location.id') == 0)
			{
				echo 'set Cat ID';
				$data->set('catid', $app->getInput()->get('catid', $app->getUserState('com_footballmanager.locations.filter.category_id'), 'int'));
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

	protected function preprocessForm($form, $data, $group = 'content'): void
	{
		parent::preprocessForm($form, $data, $group);
	}

	protected function prepareTable($table)
	{
		$table->generateAlias();
	}

	public function save($data)
	{

		$app   = Factory::getApplication();
		$input = $app->getInput();
		$user  = $app->getIdentity();

		// sponsors
		$data['sponsors'] = json_encode($data['sponsors']);

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
				list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
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

			if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']]))
			{
				$msg = Text::_('COM_FOOTBALLMANAGER_SAVE_WARNING');
			}

			list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
			$data['alias'] = $alias;

			if (isset($msg))
			{
				$app->enqueueMessage($msg, 'warning');
			}
		}

		return parent::save($data);
	}
}
