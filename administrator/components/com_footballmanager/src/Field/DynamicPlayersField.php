<?php
/**
 * @package      Joomla.Administrator
 *              Joomla.Site
 * @subpackage   com_footballmanager
 * @author       Marco Rensch
 * @since        1.0.0
 *
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright    Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\Footballmanager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class DynamicPlayersField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'DynamicPlayers';
	protected $table = '#__footballmanager_players';
	protected $teamId = 0;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.0
	 */
	protected function getOptions()
	{

		$this->teamId = $this->defineTeamId();

		if ($this->teamId)
		{
			$players = $this->loadTeamPlayers();
		}
		else
		{
			$players = $this->loadAllPlayers();
		}

		$options   = [];
		$options[] = HTMLHelper::_('select.option', "", Text::_('COM_FOOTBALLMANAGER_FIELD_PLAYER_HEADER_SELECT'));
//		foreach ($players as $player)
//		{
//			$optionString = (isset($player->player_number) ? '#'.$player->player_number.' '  : '') . $player->team_name . ' | ' . $player->firstname . ' ' . $player->lastname;
//			$options[] = HTMLHelper::_('select.option', $player->id, $optionString);
//		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 *
	 * @description gets the team id from the form based on the submitted reference (home / away)
	 *
	 * @return int      The team id
	 *
	 * @since       1.0.0
	 */
	protected function defineTeamId()
	{
		return $this->form->getValue('roster-team-id');
	}

	protected function loadAllPlayers()
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id, firstname, lastname');
		$query->from($db->quoteName($this->table));
		$query->order('ordering ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	protected function loadTeamPlayers()
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('p.id, p.firstname, p.lastname, p.published, pt.player_number, pos.title, t.title AS team_name');
		$query->from('#__footballmanager_players_teams AS pt');
		$query->where('pt.team_id = ' . $db->quote($this->teamId));
		$query->where('pt.until IS NULL');
		$query->join('LEFT', '#__footballmanager_players AS p ON p.id = pt.player_id');
		$query->where('p.published = 1');
		$query->join('LEFT', '#__footballmanager_positions AS pos ON pos.id = pt.position_id');
		$query->join('LEFT', '#__footballmanager_teams AS t ON t.id = pt.team_id');
		$query->order('pt.ordering ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	protected function addScripts()
	{
		// later
	}
}