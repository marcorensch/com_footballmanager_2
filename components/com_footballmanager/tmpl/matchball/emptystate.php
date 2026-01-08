<?php

/**
 * @package     com_footballmanager
 *
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;

$params = Factory::getApplication()->getParams();
$input  = Factory::getApplication()->input;

$this->matchballTitle      = $input->getString('matchball_title', '');
$this->matchballTextNoContent = $input->getRaw('matchball_no_content', '');

$wa = $this->getDocument()->getWebAssetManager();
if (intval($params->get('load_uikit', '1')))
{
    $wa->useScript('com_footballmanager.uikitjs')
            ->useScript('com_footballmanager.uikitIconsjs')
            ->useStyle('com_footballmanager.uikitcss');
}

$wa->useStyle('com_footballmanager.main');
?>

<div class="matchball-container" uk-height-viewport="expand:true;top:true;bottom:true">

    <?php if ($this->matchballTitle) : ?>
        <div class="matchball-header">
            <h2 class="matchball-title"><?php echo $this->matchballTitle; ?></h2>
        </div>
    <?php endif; ?>

    <?php if ($this->matchballTextNoContent) : ?>
        <div class="matchball-text-no-content"><?php echo $this->matchballTextNoContent; ?></div>
    <?php endif; ?>
</div>
