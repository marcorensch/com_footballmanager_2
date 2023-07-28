<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
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
 * Script file of Company location Component
 *
 * @since  __BUMP_VERSION__
 */
class Com_FootballmanagerInstallerScript extends InstallerScript
{
	/**
	 * Minimum Joomla version to check
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	private $minimumJoomlaVersion = '4.0';

	/**
	 * Minimum PHP version to check
	 *
	 * @var    string
	 * @since  __BUMP_VERSION__
	 */
	private $minimumPHPVersion = JOOMLA_MINIMUM_PHP;

	/**
	 * Method to install the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 * @since  __BUMP_VERSION__
	 */
	public function install($parent): bool
	{
		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_INSTALL');

		// Install Uncategorised categories for each type of content that supports categories.
		$this->installUncategorisedCat('com_footballmanager.locations');
		$this->installUncategorisedCat('com_footballmanager.sponsors');
		$this->installUncategorisedCat('com_footballmanager.teams');


		$this->addDashboardMenu('footballmanager', 'footballmanager');

		return true;
	}

	private function installUncategorisedCat($view): void
	{
		$alias   = ApplicationHelper::stringURLSafe('Uncategorised');

		// Initialize a new category.
		$category = Table::getInstance('Category');

		$data = [
			'extension' => $view,
			'title' => 'Uncategorised',
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
			return;
		}

		// Check to make sure our location data is valid.
		if (!$category->check()) {
			return;
		}

		// Store the location category.
		if (!$category->store(true)) {
		}
	}

	/**
	 * Method to uninstall the extension
	 *
	 * @param   InstallerAdapter  $parent  The class calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since  __BUMP_VERSION__
	 */
	public function uninstall($parent): bool
	{
		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_UNINSTALL');

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
	 * @since  __BUMP_VERSION__
	 *
	 */
	public function update($parent): bool
	{
		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_UPDATE');

		if(!$this->checkIfDashboardModuleExists('footballmanager'))
		{
			$this->addDashboardMenu('footballmanager', 'footballmanager');
		}

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
	 * @since  __BUMP_VERSION__
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
					'jerror'
				);

				return false;
			}

			// Check for the minimum Joomla version before continuing
			if (!empty($this->minimumJoomlaVersion) && version_compare(JVERSION, $this->minimumJoomlaVersion, '<')) {
				Log::add(
					Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomlaVersion),
					Log::WARNING,
					'jerror'
				);

				return false;
			}
		}else{
			$this->removeDashboardModules('footballmanager');
		}

		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_PREFLIGHT');

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
	 * @since  __BUMP_VERSION__
	 *
	 */
	public function postflight($type, $parent): bool
	{
		echo Text::_('COM_FOOTBALLMANAGER_INSTALLERSCRIPT_POSTFLIGHT');

		return true;
	}

	private function removeDashboardModules($dashboard)
	{
		$title = $this->getDefaultModuleTitle($dashboard);

		try{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__modules'))
			->where($db->quoteName('title') . ' = ' . $db->quote($title));
		$db->setQuery($query);
		$db->execute();
		}catch (\Exception $e){
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	private function getDefaultModuleTitle($dashboard): string
	{
		// Try to get a translated module title, otherwise fall back to a fixed string.
		$titleKey         = strtoupper('COM_' . $this->extension . '_DASHBOARD_' . $dashboard . '_TITLE');
		$title            = Text::_($titleKey);
		return ($title === $titleKey) ? ucfirst($dashboard) . ' Dashboard' : $title;
	}

	private function checkIfDashboardModuleExists($dashboard): bool
	{
		$title = $this->getDefaultModuleTitle($dashboard);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('title') . ' = ' . $db->quote($title));
		$db->setQuery($query);
		$list = $db->loadObjectList();

		return count($list) > 0;
	}
}
