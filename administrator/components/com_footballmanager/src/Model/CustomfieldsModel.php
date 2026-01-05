<?php
/**
 * @package     com_footballmanager
 * 
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
 * @since  2.0.0
 */
class CustomfieldsModel extends ListModel
{
	protected $sections = [];

	public function getSections()
	{
//		$this->sections[] = $this->createSection('Team', 'team', 'users');
//		$this->sections[] = $this->createSection('Referee', 'referee', 'user');
		$this->sections[] = $this->createSection('Coach', 'coach' , 'chalkboard-teacher');
		$this->sections[] = $this->createSection('Locations', 'location', 'map-marker-alt');
		$this->sections[] = $this->createSection('Sponsors', 'sponsor', 'handshake');
		$this->sections[] = $this->createSection('Teams', 'team', 'users');
		$this->sections[] = $this->createSection('Players', 'player', 'user');


		return $this->sections;
	}

	private function createSection($title, $type, $icon = 'comment'): stdClass
	{
		$section = new stdClass();
		$section->title = $title;
		$section->icon = $icon;
		$section->fieldsdUri = 'index.php?option=com_fields&context=com_footballmanager.'.$type;
		$section->addFieldUri = 'index.php?option=com_fields&layout=edit&context=com_footballmanager.'.$type;
		$section->groupsUri = 'index.php?option=com_fields&view=groups&context=com_footballmanager.'.$type;
		$section->addGroupUri = 'index.php?option=com_fields&view=groups&layout=edit&context=com_footballmanager.'.$type;
		return $section;
	}

}