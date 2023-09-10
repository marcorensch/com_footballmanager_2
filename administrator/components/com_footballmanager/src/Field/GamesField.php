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

class GamesField extends ListField{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'Games';

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
		$query->select('a.id, a.kickoff, home.title AS home, away.title AS away');
		$query->from('#__footballmanager_games AS a');
		$query->order('kickoff DESC');
		$query->join('LEFT', '#__footballmanager_teams AS home ON home.id = home_team_id');
		$query->join('LEFT', '#__footballmanager_teams AS away ON away.id = away_team_id');
		$db->setQuery($query);
		$games = $db->loadObjectList();

		$currentGameId = $this->getCurrentGameId();

		$options = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_FOOTBALLMANAGER_FIELD_DEFAULT_SELECT_GAME'));
		foreach ($games as $game)
		{
			if($game->id !== $currentGameId)
			{
				$options[] = HTMLHelper::_('select.option', $game->id, $game->home . ' - ' . $game->away . ' (' . $game->kickoff . ')');
			}
		}


		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	protected function getCurrentGameId()
	{
		$app = Factory::getApplication();
		$gameId = 0;
		if($app->input->get('layout') === 'edit' && $app->input->get('view') === 'game'){
			$gameId = $app->input->getInt('id', 0);
		}
		return $gameId;
	}
}