<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_hello
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\View\Location;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	public function display($tpl = null): void
	{
		$this->item = $this->get('Location');
		parent::display($tpl);
	}
}