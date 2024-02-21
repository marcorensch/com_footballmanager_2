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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

class GamesField extends ListField
{
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
		$show_select = $this->getAttribute('show_select', 'false') === 'true';
		$dateFilterAttribute = $this->getAttribute('datefilterfieldname', '') ?? '';
		$dateString          = $dateFilterAttribute ? $this->form->getValue($dateFilterAttribute) : null;
		if ($dateString)
		{
			$kickoffDate     = new \DateTime($dateString);
			$dateFilterStart = $kickoffDate->modify('-12 hours')->format('Y-m-d H') . ':00:00';
			$dateFilterEnd   = $kickoffDate->modify('+12 hours')->format('Y-m-d H') . ':59:59';
		}
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('a.id, a.kickoff, home.title AS home, away.title AS away');
		$query->from('#__footballmanager_games AS a');
		if ($dateString)
		{
			$query->where('a.kickoff BETWEEN ' . $db->quote($dateFilterStart) . ' AND ' . $db->quote($dateFilterEnd));
		}
		$query->order('kickoff DESC');
		$query->join('LEFT', '#__footballmanager_teams AS home ON home.id = home_team_id');
		$query->join('LEFT', '#__footballmanager_teams AS away ON away.id = away_team_id');
		// Join League Information
		$query->select('league.title AS league_title')
			->join('LEFT', '#__footballmanager_leagues AS league ON league.id = a.league_id');

		$db->setQuery($query);
		$games = $db->loadObjectList();

		$currentGameId = $this->getCurrentGameId();

		$options   = [];
		if($show_select) $options[] = HTMLHelper::_('select.option', '', Text::_('COM_FOOTBALLMANAGER_FIELD_DEFAULT_SELECT_GAME'));
		foreach ($games as $game)
		{
			if ($game->id !== $currentGameId)
			{
				$game->kickoff = HTMLHelper::date($game->kickoff, 'DATE_FORMAT_LC5');
				$options[] = HTMLHelper::_('select.option', $game->id, $game->league_title . ' | ' . $game->home . ' - ' . $game->away . ' (' . $game->kickoff . ')');
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	protected function getCurrentGameId()
	{
		$app    = Factory::getApplication();
		$gameId = 0;
		if ($app->input->get('layout') === 'edit' && $app->input->get('view') === 'game')
		{
			$gameId = $app->input->getInt('id', 0);
		}

		return $gameId;
	}
}