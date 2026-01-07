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
use Joomla\Database\DatabaseInterface;

class SponsorsField extends ListField{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'Sponsors';

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
		$query->from('#__footballmanager_sponsors');
		$query->order('ordering ASC');
		$db->setQuery($query);
		$sponsors = $db->loadObjectList();

		$options = [];
		foreach ($sponsors as $sponsor)
		{
			$options[] = HTMLHelper::_('select.option', $sponsor->id, $sponsor->title);
		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}