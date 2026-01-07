<?php

/**
 * * @package     NXD.FootballManager
 *
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @var stdClass $item
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use NXD\Component\Footballmanager\Site\Helper\ManagerHelper;

// $item is automatically available through the foreach loop in default.php.

?>

<div class="matchball-grid-item">
    <div class="uk-card uk-card-small uk-card-default">
        <div class="uk-card-header">
            <div class="uk-grig-collapse uk-flex uk-flex-middle" uk-grid>
                <div class="uk-width-expand">
                    <h3 class="uk-card-title">
                        <?php echo HTMLHelper::_('date', $this->item->kickoff, Text::_('DATE_FORMAT_LC4')); ?>
                    </h3>
                    <div class="uk-text-muted">
                        <?php echo $this->item->league_title; ?> |
                        <?php echo $this->item->location_title; ?>
                    </div>
                </div>
                <div class="uk-width-auto uk-text-muted">
                    <i class="fas fa-football fa-3x"></i>
                </div>
            </div>
        </div>
        <div class="uk-card-body">
            <div class="uk-text-bold uk-text-center"><?php echo $this->item->home_team_title; ?></div>
            <div class="uk-text-center">vs</div>
            <div class="uk-text-bold uk-text-center"><?php echo $this->item->away_team_title; ?></div>
        </div>
        <div class="uk-card-footer">
            <?php
            if ($this->item->matchball_sponsor_id)
            {
                if ($this->item->matchball_sponsor_website)
                {
                    echo '<div class="uk-text-center uk-text-large">' . Text::_('COM_FOOTBALLMANAGER_SPONSORED_BY') . '</div>';
                    echo ManagerHelper::renderLink(
                            $this->item->matchball_sponsor_website,
                            $this->item->matchball_sponsor_title ?? $this->item->matchball_sponsor_website,
                            ['target' => '_blank', 'class' => 'uk-button uk-button-default uk-button-large uk-width-expand']
                    );
                }
            }
            else
            {
                if ($this->showBecomeSponsor && $this->becomeSponsorLink)
                {
                    echo ManagerHelper::renderLink(
                            $this->becomeSponsorLink,
                            $this->becomeSponsorText ?? $this->becomeSponsorLink,
                            ['target'  => '_blank',
                             'subject' => Text::sprintf('COM_FOOTBALLMANAGER_MATCHBALL_LINK_SUBJECT', $this->item->league_title, HTMLHelper::_('date', $this->item->kickoff, Text::_('DATE_FORMAT_LC4'))),
                             'class'   => 'uk-button uk-button-primary uk-button-large uk-width-expand'
                            ]
                    );
                }
            }
            ?>
        </div>
    </div>
</div>