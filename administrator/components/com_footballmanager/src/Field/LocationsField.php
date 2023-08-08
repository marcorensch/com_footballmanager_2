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
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class LocationsField extends ListField{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'Locations';
	protected $table = '#__footballmanager_locations';

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
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName($this->table));
		$query->order('title ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		$options = [];
		$options[] = HTMLHelper::_('select.option', "", Text::_('COM_FOOTBALLMANAGER_FIELD_LOCATION_HEADER_SELECT'));
		foreach ($items as $item)
		{
			$options[] = HTMLHelper::_('select.option', $item->id, $item->title);
		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}