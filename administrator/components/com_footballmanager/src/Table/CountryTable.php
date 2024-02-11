<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Table;

\defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

/**
 * FootballManager Country Table class.
 *
 * @since  __BUMP_VERSION__
 */
class CountryTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_footballmanager.country';

		parent::__construct('#__footballmanager_countries', 'id', $db);
	}

	public function check()
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
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	public function store($updateNulls = true)
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
