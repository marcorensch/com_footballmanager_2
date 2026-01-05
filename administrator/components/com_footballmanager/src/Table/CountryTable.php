<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

/**
 * FootballManager Country Table class.
 *
 * @since  2.0.0
 */
class CountryTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since   2.0.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_footballmanager.country';

		parent::__construct('#__footballmanager_countries', 'id', $db);
	}

	public function check(): bool
	{
		// Set null for empty fields
		if ($this->numcode === '')
		{
			$this->numcode = null;
		}
		try
		{
			parent::check();
		}
		catch (\Exception $e)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	public function store($updateNulls = true): bool
	{
		// Transform the params field
		if (is_array($this->params))
		{
			$registry     = new Registry($this->params);
			$this->params = (string) $registry;
		}
		else
		{
			$this->params = '';
		}

		return parent::store($updateNulls);
	}
}
