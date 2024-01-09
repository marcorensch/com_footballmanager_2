<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Exception;
use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\Fields\FieldsServiceInterface;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Language\Text;
use NXD\Component\Footballmanager\Administrator\Service\HTML\AdministratorService;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_footballmanager
 *
 * @since  1.0.0
 */
class FootballmanagerComponent extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface, FieldsServiceInterface, RouterServiceInterface
{
	use CategoryServiceTrait;
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */

	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('footballmanageradministrator', new AdministratorService);
	}

	protected function getTableNameForSection(string $section = null): string
	{
		$tableName = 'footballmanager_locations';
		switch ($section)
		{
			case 'category':
				$tableName = 'categories';
				break;
			case 'team':
				$tableName = 'footballmanager_teams';
				break;
			case 'player':
				$tableName = 'footballmanager_players';
				break;
			case 'season':
				$tableName = 'footballmanager_seasons';
				break;
			case 'season_phases':
				$tableName = 'footballmanager_season_phases';
				break;
			case 'league':
				$tableName = 'footballmanager_leagues';
				break;
			case 'coach':
				$tableName = 'footballmanager_coaches';
				break;
			case 'official':
				$tableName = 'footballmanager_officials';
				break;
			case 'game':
				$tableName = 'footballmanager_games';
				break;
			case 'position':
				$tableName = 'footballmanager_positions';
				break;
			default:
			case 'location':
				$tableName = 'footballmanager_locations';
				break;
		}

		return $tableName;
	}

	protected function getStateColumnForSection(string $section = null)
	{
		return 'published';
	}

	public function countItems(array $items, string $section)
	{
		try
		{
			$config = (object) array(
				'related_tbl'   => $this->getTableNameForSection($section),
				'state_col'     => 'published',
				'group_col'     => 'catid',
				'relation_type' => 'category_or_group',
			);
			ContentHelper::countRelations($items, $config);
		}
		catch (Exception $e)
		{
			// do nothing
		}
	}

	public function validateSection($section, $item = null): ?string
	{
		return in_array($section, ['location', 'team', 'player', 'coach','official','game','season','season_phase','league'], true) ? $section : null;
	}

	public function getContexts(): array
	{
		Factory::getApplication()->getLanguage()->load('com_footballmanager', JPATH_ADMINISTRATOR);

		$contexts = array(
			'com_footballmanager.location' => Text::_('COM_FOOTBALLMANAGER_LOCATIONS'),
			'com_footballmanager.team' => Text::_('COM_FOOTBALLMANAGER_TEAMS'),
			'com_footballmanager.player' => Text::_('COM_FOOTBALLMANAGER_PLAYERS'),
			'com_footballmanager.coach' => Text::_('COM_FOOTBALLMANAGER_COACHES'),
			'com_footballmanager.referee' => Text::_('COM_FOOTBALLMANAGER_REFEREES'),
			'com_footballmanager.game' => Text::_('COM_FOOTBALLMANAGER_GAMES'),
		);

		return $contexts;
	}

}