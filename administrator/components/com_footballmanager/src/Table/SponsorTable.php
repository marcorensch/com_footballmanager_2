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

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

/**
 * FootballManager Location Table class.
 *
 * @since  2.0.0
 */
class SponsorTable extends Table
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
        $this->typeAlias = 'com_footballmanager.sponsor';

        parent::__construct('#__footballmanager_sponsors', 'id', $db);
    }

    public function check(): bool
    {
	    $app = Factory::getApplication();

        try {
            parent::check();
        } catch (\Exception $e) {
	        $app->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        // Check the publishing down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
	        $app->enqueueMessage(Text::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'), 'error');
            return false;
        }

        // Set publish_up, publish_down to null if not set
        if (!$this->publish_up) {
            $this->publish_up = null;
        }

        if (!$this->publish_down) {
            $this->publish_down = null;
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
