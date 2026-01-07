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
use Joomla\CMS\Language\Text;

$params = Factory::getApplication()->getParams();
$input  = Factory::getApplication()->input;

$this->showBecomeSponsor = $input->getInt('show_become_sponsor', 1);
$this->becomeSponsorText = $input->getString('become_sponsor_text', 'Become a Sponsor');
$this->becomeSponsorLink = $input->getString('become_sponsor_link', '');

$this->matchballTitle      = $input->getString('matchball_title', '');
$this->matchballTextBefore = $input->getRaw('matchball_before', '');
$this->matchballTextAfter  = $input->getRaw('matchball_after', '');

$wa = $this->document->getWebAssetManager();
if (intval($params->get('load_uikit', '1')))
{
    $wa->useScript('com_footballmanager.uikitjs')
            ->useScript('com_footballmanager.uikitIconsjs')
            ->useStyle('com_footballmanager.uikitcss');
}

if ($this->showBecomeSponsor)
{
    Text::script('COM_FOOTBALLMANAGER_MATCHBALL_COPY_MAIL_SUCCESS');
    Text::script('COM_FOOTBALLMANAGER_MATCHBALL_COPY_MAIL_ERROR');
    // Add the Copy to the clipboard script only if we allow that users can contact us about sponsorships
    $wa->useScript('com_footballmanager.copy-to-clipboard');
}

$containerThemeCls = "";
if ($params->get('theme', 'light') === 'dark')
{
    $containerThemeCls = " uk-light";
}

$wa->useStyle('com_footballmanager.main');
?>

<div class="matchball-container" uk-height-viewport="expand:true;top:true;bottom:true">

    <?php if ($this->matchballTitle) : ?>
        <div class="matchball-header">
            <h2 class="matchball-title"><?php echo $this->matchballTitle; ?></h2>
        </div>
    <?php endif; ?>

    <?php if ($this->matchballTextBefore) : ?>
        <div class="matchball-text-before"><?php echo $this->matchballTextBefore; ?></div>
    <?php endif; ?>

    <div class="matchball-list">
        <div class="uk-visible@l">
            <table class="uk-table uk-table-striped uk-table-middle">
                <thead>
                <tr>
                    <th><!-- Date --></th>
                    <th><!-- League --></th>
                    <th><!-- Home --></th>
                    <th><!-- vs --></th>
                    <th><!-- Away --></th>
                    <th style="width:230px"><!-- Sponsor or Link --></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->items as $item)
                {
                    $this->item = $item;
                    echo $this->loadTemplate('item');
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="uk-hidden@l">
            <div class="uk-grid-small uk-grid-match uk-child-width-1-1 uk-child-width-1-2@m" uk-grid>
                <?php foreach ($this->items as $item)
                {
                    $this->item = $item;
                    echo $this->loadTemplate('item_mobile');
                }
                ?>
            </div>
        </div>
    </div>

    <?php if ($this->matchballTextAfter) : ?>
        <div class="matchball-text-after"><?php echo $this->matchballTextAfter; ?></div>
    <?php endif; ?>
</div>
