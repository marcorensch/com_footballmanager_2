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
	private $minimumJoomlaVersion = '3.10';

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
		$this->installCategoryForView('com_footballmanager.locations');
		$this->installCategoryForView('com_footballmanager.sponsors');
		$this->installCategoryForView('com_footballmanager.teams');
		$this->installCategoryForView('com_footballmanager.games');
		$this->installCategoryForView('com_footballmanager.referees');
		$this->installCategoryForView('com_footballmanager.coaches');
		$this->installCategoryForView('com_footballmanager.positions');
		$this->installCategoryForView('com_footballmanager.positions', 'Player');
		$this->installCategoryForView('com_footballmanager.positions', 'Coach');
		$this->installCategoryForView('com_footballmanager.positions', 'Official');

		return true;
	}

	private function installCategoryForView($view, $title = "Uncategorised"): void
	{
		$alias   = ApplicationHelper::stringURLSafe($title);

		// Initialize a new category.
		$category = Table::getInstance('Category');

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
}
