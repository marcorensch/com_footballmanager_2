<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\DatabaseInterface;

/**
 * Script file of the Football Manager 2 Component
 *
 * @since  2.0.0
 */
class Com_FootballmanagerInstallerScript extends InstallerScript
{

	/**
	 * Minimum Joomla version to check
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	private $minimumJoomlaVersion = '4.2';

	/**
	 * Minimum PHP version to check
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	private $minimumPHPVersion = JOOMLA_MINIMUM_PHP;

	private $playerPositionsCategoryId = 0;
	private $coachPositionsCategoryId = 0;
	private $officialPositionsCategoryId = 0;

	private $cheerleaderPositionsCategoryId = 0;

	/**
	 * Method to install the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 * @since  2.0.0
	 */
	public function install($parent): bool
	{
//		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_INSTALL');

		// Install Uncategorised categories for each type of content that supports categories.
		$this->installCategoryForView('com_footballmanager.locations');
		$this->installCategoryForView('com_footballmanager.sponsors');
		$this->installCategoryForView('com_footballmanager.teams');
		$this->installCategoryForView('com_footballmanager.games');
		$this->installCategoryForView('com_footballmanager.officials');
		$this->installCategoryForView('com_footballmanager.players');
		$this->installCategoryForView('com_footballmanager.coaches');
		$this->installCategoryForView('com_footballmanager.cheerleaders');
		$this->installCategoryForView('com_footballmanager.positions');
		$this->installCategoryForView('com_footballmanager.countries');
		$this->cheerleaderPositionsCategoryId = $this->installCategoryForView('com_footballmanager.positions', 'Cheerleader');
		$this->playerPositionsCategoryId = $this->installCategoryForView('com_footballmanager.positions', 'Player');
		$this->coachPositionsCategoryId = $this->installCategoryForView('com_footballmanager.positions', 'Coach');
		$this->officialPositionsCategoryId = $this->installCategoryForView('com_footballmanager.positions', 'Official');

		return true;
	}

	private function getCategoryIfExists($view, $alias): int|bool|null
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id')
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote($view))
			->where($db->quoteName('alias') . ' = ' . $db->quote($alias));
		$db->setQuery($query);
		$id = $db->loadResult();
		return $id;
	}

	private function installCategoryForView($view, $title = "Uncategorised"): int|bool|null
	{
		$app = Factory::getApplication();
		$alias   = ApplicationHelper::stringURLSafe($title);

		if($existingId = $this->getCategoryIfExists($view, $alias)){
			return $existingId;
		}

		// Boot CategoryComponentHelper
		$category = Factory::getApplication()->bootComponent('com_categories')->getMVCFactory()->createTable('Category');

		// Initialize a new category.
//		$category = Table::getInstance('Category');
		$data = [
			'extension' => $view,
			'title' => $title,
			'alias' => $alias,
			'path' => $alias,
			'description' => '',
			'published' => 1,
			'access' => 1,
			'params' => '{"target":"","image":""}',
			'metadesc' => '',
			'metakey' => '',
			'metadata' => '{"page_title":"","author":"","robots":""}',
			'created_time' => Factory::getDate()->toSql(),
			'created_user_id' => (int) Factory::getApplication()->getIdentity()->id,
			'language' => '*',
			'rules' => [],
			'parent_id' => 1,
		];

		$category->setLocation(1, 'last-child');

		// Bind the location data to the table
		if (!$category->bind($data)) {
			return false;
		}

		// Check to make sure our location data is valid.
		if (!$category->check()) {
			$app->enqueueMessage($category->getError(), 'error');
			return false;
		}

		// Store the category.
		if (!$category->store(true)) {
			$app->enqueueMessage($category->getError(), 'error');
			return false;
		}

		return $category->id;
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  2.0.0
	 */
	public function uninstall($parent): bool
	{
//		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_UNINSTALL');

		return true;
	}

	/**
	 * Method to update the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 * @since  2.0.0
	 *
	 */
	public function update($parent): bool
	{
//		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_UPDATE');
		$this->installCategoryForView('com_footballmanager.countries');

		return true;
	}

	/**
	 * Function called before extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  2.0.0
	 *
	 * @throws Exception
	 */
	public function preflight($type, $parent): bool
	{
		if ($type !== 'uninstall') {
			// Check for the minimum PHP version before continuing
			if (!empty($this->minimumPHPVersion) && version_compare(PHP_VERSION, $this->minimumPHPVersion, '<')) {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPHPVersion),
					Log::WARNING,
					'warning'
				);

				return false;
			}

			// Check for the minimum Joomla version before continuing
			if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<')) {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
					Log::WARNING,
					'error'
				);

				return false;
			}
		}else{
			// Delete all categories
			// Delete all associated fields
		}

//		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_PREFLIGHT');

		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences
	 *
	 * @param   string            $type    The type of change (install, update or discover_install, not uninstall)
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  2.0.0
	 *
	 */
	public function postflight($type, $parent): bool
	{
//		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_POSTFLIGHT');

		// Install Positions in Postflight
		if($type === 'install'){
			// Install Basic set of positions for Players
			if($this->playerPositionsCategoryId)
			{
				$this->installPosition('Player', 'Quarterback', 'QB');
				$this->installPosition('Player', 'Offensive Line', 'OL');
				$this->installPosition('Player', 'Running Back', 'RB');
				$this->installPosition('Player', 'Wide Receiver', 'WR');
				$this->installPosition('Player', 'Tight End', 'TE');
				$this->installPosition('Player', 'Center', 'C');
				$this->installPosition('Player', 'Guard', 'G');
				$this->installPosition('Player', 'Tackle', 'T');
				$this->installPosition('Player', 'Defensive Line', 'DL');
				$this->installPosition('Player', 'Defensive End', 'DE');
				$this->installPosition('Player', 'Defensive Tackle', 'DT');
				$this->installPosition('Player', 'Linebacker', 'LB');
				$this->installPosition('Player', 'Cornerback', 'CB');
				$this->installPosition('Player', 'Safety', 'S');
				$this->installPosition('Player', 'Kicker', 'K');
				$this->installPosition('Player', 'Punter', 'P');
				$this->installPosition('Player', 'Kick Returner', 'KR');
				$this->installPosition('Player', 'Punt Returner', 'PR');
				$this->installPosition('Player', 'Long Snapper', 'LS');
				$this->installPosition('Player', 'Holder', 'H');
				$this->installPosition('Player', 'Upback', 'UB');
				$this->installPosition('Player', 'Gunner', 'G');
				$this->installPosition('Player', 'Jammer', 'J');
			}

			// Install Basic set of positions for Coaches
			if($this->coachPositionsCategoryId)
			{
				$this->installPosition('Coach', 'Head Coach', 'HC');
				$this->installPosition('Coach', 'Offensive Coordinator', 'OC');
				$this->installPosition('Coach', 'Defensive Coordinator', 'DC');
				$this->installPosition('Coach', 'Special Teams Coordinator', 'STC');
				$this->installPosition('Coach', 'Quarterbacks Coach', 'QB');
				$this->installPosition('Coach', 'Running Backs Coach', 'RB');
				$this->installPosition('Coach', 'Wide Receivers Coach', 'WR');
				$this->installPosition('Coach', 'Tight Ends Coach', 'TE');
				$this->installPosition('Coach', 'Offensive Line Coach', 'OL');
				$this->installPosition('Coach', 'Defensive Line Coach', 'DL');
				$this->installPosition('Coach', 'Linebackers Coach', 'LB');
				$this->installPosition('Coach', 'Defensive Backs Coach', 'DB');
				$this->installPosition('Coach', 'Special Teams Coach', 'ST');
				$this->installPosition('Coach', 'Assistant Coach', 'AC');
			}

			// Install Basic set of positions for Officials
			if($this->officialPositionsCategoryId)
			{
				$this->installPosition('Official', 'Referee', 'R');
				$this->installPosition('Official', 'Assistant Referee', 'AR');
				$this->installPosition('Official', 'Umpire', 'U');
				$this->installPosition('Official', 'Down Judge', 'DJ');
				$this->installPosition('Official', 'Line Judge', 'LJ');
				$this->installPosition('Official', 'Field Judge', 'FJ');
				$this->installPosition('Official', 'Side Judge', 'SJ');
				$this->installPosition('Official', 'Back Judge', 'BJ');
			}

			// Install Basic set of positions for Cheerleaders
			if($this->cheerleaderPositionsCategoryId)
			{
				$this->installPosition('Cheerleader', 'Base');
				$this->installPosition('Cheerleader', 'Flyer');
				$this->installPosition('Cheerleader', 'Backspot');
			}

		}

		return true;
	}

	private function installPosition(string $type, string $title, string $abbreviation = ''): void
	{
		$alias   = ApplicationHelper::stringURLSafe($title);
		$now = Factory::getDate()->toSql();

		error_log("Installing Position: " . $title . " (" . $abbreviation . ")" . $now);

		$categoryId = false;
		if($type === 'Player'){
			$categoryId = $this->playerPositionsCategoryId;
		}elseif($type === 'Cheerleader'){
			$categoryId = $this->cheerleaderPositionsCategoryId;
		}elseif($type === 'Coach'){
			$categoryId = $this->coachPositionsCategoryId;
		}elseif ($type === 'Official'){
			$categoryId = $this->officialPositionsCategoryId;
		}

		if(!$categoryId){
			return;
		}

		$data = [
			'title' => $title,
			'shortname' => $abbreviation,
			'alias' => $alias,
			'published' => 1,
			'access' => 1,
			'created_at' => $now,
			'created_by' => (int) Factory::getApplication()->getIdentity()->id,
			'modified_by' => (int) Factory::getApplication()->getIdentity()->id,
			'catid' => $categoryId,
		];

		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array_keys($data);
		$values = array_values($data);
		$query->insert($db->quoteName('#__footballmanager_positions'))
			->columns($db->quoteName($columns))
			->values(implode(',', $db->quote($values)));
		$db->setQuery($query);
		$db->execute();
	}
}
