<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\Footballmanager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

class PhasesField extends ListField{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'Phases';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.0
	 */
	protected function getOptions()
	{
		$show_select = $this->getAttribute('show_select', 'true') === 'true';
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__footballmanager_season_phases');
		$query->order('ordering ASC');
		$db->setQuery($query);
		$teams = $db->loadObjectList();

		$options = [];
		if($show_select)
		{
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_FOOTBALLMANAGER_FIELD_DEFAULT_SELECT_SEASON_PHASE'));
		}
		foreach ($teams as $team)
		{
			$options[] = HTMLHelper::_('select.option', $team->id, $team->title);
		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}