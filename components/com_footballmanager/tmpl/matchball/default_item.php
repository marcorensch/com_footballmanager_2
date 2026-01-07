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

<tr class="matchball-item">
    <td>
        <div>
            <?php echo HTMLHelper::_('date', $this->item->kickoff, Text::_('DATE_FORMAT_LC4')); ?>
        </div>
        <div class="uk-text-small">
            <?php echo $this->item->location_title; ?>
        </div>
    </td>
    <td>

    </td>
    <td>
        <?php echo $this->item->league_title; ?>
    </td>
    <td class="uk-text-right">
        <?php echo $this->item->home_team_title; ?>
    </td>
    <td>vs</td>
    <td>
        <?php echo $this->item->away_team_title; ?>
    </td>
    <td>
        <?php
        if ($this->item->matchball_sponsor_id)
        {
            if ($this->item->matchball_sponsor_website)
            {
                echo ManagerHelper::renderLink(
                        $this->item->matchball_sponsor_website,
                        $this->item->matchball_sponsor_title ?? $this->item->matchball_sponsor_website,
                        ['target' => '_blank', 'class' => 'uk-button uk-button-default uk-button-small uk-width-expand']
                );
            }
        }
        else
        {
            echo '<div class="uk-button-group uk-width-expand">';
            if ($this->showBecomeSponsor && $this->becomeSponsorLink)
            {
                echo ManagerHelper::renderLink(
                        $this->becomeSponsorLink,
                        $this->becomeSponsorText ?? $this->becomeSponsorLink,
                        ['target'  => '_blank',
                         'subject' => Text::sprintf('COM_FOOTBALLMANAGER_MATCHBALL_LINK_SUBJECT', $this->item->league_title, HTMLHelper::_('date', $this->item->kickoff, Text::_('DATE_FORMAT_LC4'))),
                         'class'   => 'uk-button uk-button-primary uk-button-small uk-width-expand'
                        ]
                );

                if (ManagerHelper::isMailAddress($this->becomeSponsorLink))
                {
                    echo '<button title="'.Text::_("COM_FOOTBALLMANAGER_MATCHBALL_COPY_MAIL_TEXT").'" class="uk-button uk-button-primary uk-button-small" onclick="copyToClipboard(\'' . $this->becomeSponsorLink . '\')" style="cursor: pointer;">'.
                        '<i class="fas fa-copy"></i>'.
                            '</button>';
                }
            }
            echo '</div>';
        }
        ?>
    </td>
</tr>


