<?php
/**
 * @package     com_footballmanager
 *              Joomla.Site
 * 
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\Footballmanager\Administrator\Field;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class PositionsField extends ListField{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'Positions';
	protected $context;
	protected $categoryIds;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.0
	 */
	protected function getOptions()
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$this->context = $this->element['context'];
		if($this->context){
			// Get the category id for filtering the positions
			$catQuery = $db->getQuery(true);
			$catQuery->select('id')
				->from('#__categories')
				->where($db->quoteName('extension') . ' = ' . $db->quote('com_footballmanager.positions'))
				->where($db->quoteName('alias') . ' = ' . $db->quote('uncategorised'))
				->orWhere($db->quoteName('alias') . ' = ' . $db->quote($this->context));
			$db->setQuery($catQuery);
			$result = $db->loadAssocList();
			$this->categoryIds = [];
			foreach($result as $cat){
				$this->categoryIds[] = $cat['id'];
			}
		}
		// Get the positions from database where the category is the same as the context or the category is uncategorised
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__footballmanager_positions');
		if($this->categoryIds){
			$query->where('catid IN (' . implode(',', $this->categoryIds) .')');
		}
		$query->order('ordering ASC');
		$db->setQuery($query);
		$teams = $db->loadObjectList();

		$options = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_FOOTBALLMANAGER_FIELD_DEFAULT_SELECT_POSITION'));
		foreach ($teams as $team)
		{
			$options[] = HTMLHelper::_('select.option', $team->id, $team->title);
		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}