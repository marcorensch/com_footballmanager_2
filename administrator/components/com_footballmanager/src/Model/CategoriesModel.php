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

use Joomla\CMS\MVC\Model\ListModel;
use stdClass;

/**
 * Item Model for the customfields dashboard.
 *
 * @since  __BUMP_VERSION__
 */
class CategoriesModel extends ListModel
{
	protected $sections = [];

	public function getSections()
	{
		$this->sections[] = $this->createSection('Locations', 'locations', 'map-marker-alt');
		$this->sections[] = $this->createSection('Sponsors', 'sponsors', 'handshake');
		$this->sections[] = $this->createSection('Teams', 'teams', 'users');
		$this->sections[] = $this->createSection('Player', 'player', 'user');
		$this->sections[] = $this->createSection('Referee', 'referee', 'id-card-alt');
		$this->sections[] = $this->createSection('Coaches', 'coaches' , 'chalkboard-teacher');
		$this->sections[] = $this->createSection('Games', 'games', 'football-ball');

		return $this->sections;
	}

	private function createSection($title, $type, $icon = 'comment'): stdClass
	{
		$section = new stdClass();
		$section->title = $title;
		$section->icon = $icon;
		$section->categoriesdUri = 'index.php?option=com_categories&extension=com_footballmanager.'.$type;
		$section->addCategoryUri = 'index.php?option=com_categories&view=category&layout=edit&extension=com_footballmanager.'.$type;
		return $section;
	}

}