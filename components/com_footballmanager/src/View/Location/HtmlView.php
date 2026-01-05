<?php

/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\View\Location;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
	public function display($tpl = null): void
	{
		$item = $this->item = $this->get('Location');

		Factory::getApplication()->triggerEvent('onContentPrepare', ['com_footballmanager.location', &$item, &$item->params]);

		// Store the events for later
		$item->event = new \stdClass;

		$results                        = Factory::getApplication()->triggerEvent('onContentAfterTitle', ['com_footballmanager.location', &$item, &$item->params]);
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results                           = Factory::getApplication()->triggerEvent('onContentBeforeDisplay', ['com_footballmanager.location', &$item, &$item->params]);
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results                          = Factory::getApplication()->triggerEvent('onContentAfterDisplay', ['com_footballmanager.location', &$item, &$item->params]);
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		parent::display($tpl);
	}
}