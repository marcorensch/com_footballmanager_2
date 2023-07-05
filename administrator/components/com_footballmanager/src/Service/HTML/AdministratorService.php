<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace NXD\Component\Footballmanager\Administrator\Service\HTML;

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/**
 * FootballManager HTML class.
 *
 * @since  1.0.0
 *
 */
class AdministratorService
{
	public function association($locationId){
		// Defaults
		$html = '';

		// Get the association
		if($associations = Associations::getAssociations('com_footballmanager','#__footballmanager_locations','com_footballmanager.location',$locationId, 'id',null)){
			foreach($associations as $tag => $associated){
				$associations[$tag] = (int) $associated->id;
			}

			// Get the associated location items
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('c.id, c.name AS title')
				->select('l.sef AS lang_sef, lang_code')
				->from('#__footballmanager_locations AS c')
				->select('cat.title AS category_title')
				->join('LEFT', '#__categories AS cat ON cat.id = c.catid')
				->where('c.id IN (' . implode(',', array_values($associations)) . ')')
				->where('c.id != ' . (int) $locationId)
				->join('LEFT', '#__languages AS l ON l.lang_code = c.language')
				->select('l.image')
				->select('l.title AS language_title');

			$db->setQuery($query);

			try {
				$items = $db->loadObjectList('id');
			} catch (\RuntimeException $e) {
				throw new \Exception($e->getMessage(), 500, $e);
			}

			if ($items) {
				foreach ($items as &$item) {
					$text = strtoupper($item->lang_sef);
					$url = Route::_('index.php?option=com_footballmanager&task=location.edit&id=' . (int) $item->id);
					$tooltip = '<strong>' . htmlspecialchars($item->language_title, ENT_QUOTES, 'UTF-8') . '</strong><br>'
						. htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8') . '<br>' . Text::sprintf('JCATEGORY_SPRINTF', $item->category_title);
					$classes = 'badge bg-secondary';

					$item->link = '<a href="' . $url . '" title="' . $item->language_title . '" class="' . $classes . '">' . $text . '</a>'
						. '<div role="tooltip" id="tip' . (int) $item->id . '">' . $tooltip . '</div>';
				}

			}

			$html = LayoutHelper::render('joomla.content.associations', $items);

		}

		return $html;
	}
}