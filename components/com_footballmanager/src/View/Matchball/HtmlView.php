<?php

/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\View\MatchBall;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use NXD\Component\Footballmanager\Site\Model\MatchballModel;

class HtmlView extends BaseHtmlView
{
	protected $items;

	public function display($tpl = null): void
	{
		/* @var $model MatchballModel */
		$model = $this->getModel();
		$this->items = $model->getItems();

		if(!count($this->items)){
			$this->setLayout('emptystate');
		}

		parent::display($tpl);
	}
}