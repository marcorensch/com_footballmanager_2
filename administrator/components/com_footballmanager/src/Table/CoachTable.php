<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Table;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

/**
 * FootballManager Location Table class.
 *
 * @since  2.0.0
 */
class CoachTable extends Table
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
        $this->typeAlias = 'com_footballmanager.coach';

        parent::__construct('#__footballmanager_coaches', 'id', $db);
    }

    public function check(): bool
    {

	    if (!$this->country_id)  $this->country_id = NULL;

	    try {
            parent::check();
        } catch (\Exception $e) {
		    $app = Factory::getApplication();
		    $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return true;
    }

    public function store($updateNulls = true): bool
    {
	    // Transform the params field
	    if (is_array($this->params)) {
		    $registry = new Registry($this->params);
		    $this->params = (string) $registry;
	    }else{
			$this->params = '';
	    }

	    return parent::store($updateNulls);
    }
}
