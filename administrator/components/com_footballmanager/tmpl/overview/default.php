<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->addInlineStyle('
.nxd-overview-item {
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #e9ecef;
    color: #212529;
    font-size: 1.5rem;
    font-weight: 500;
    padding: 1rem;
    cursor:pointer;
    position:relative;
    transition: all .2s ease-in-out;
}
.nxd-overview-item i{
color: #1f3047;
    transition: all .2s ease-in-out;
}
.nxd-overview-item:hover {
    background-color: #e9ecef;
    border-color: #c3c5c8;
    transition: all .2s ease-in-out;
}
.nxd-overview-item:hover i:not(.ext-info){
color: #263c59;
 scale:1.05;
 transition: all .2s ease-in-out;
 }
 
 .nxd-overview-item i.ext-info{
 scale: 0.8;
 }
 
.nxd-position-cover{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
a.nxd-position-cover::before{
    display: none;
}
'
);
?>

<div class="">
    <div class="nxd-overview">
        <div class="row g-8">
            <div class="col-lg-9">
                <div class="row g-2">
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4 lg-6">
                            <i class="fas fa-trophy fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_SEASONS');?></div>
                            <a href="index.php?option=com_footballmanager&view=seasons" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-angle-double-right fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_SEASON_PHASES');?></div>
                            <a href="index.php?option=com_footballmanager&view=seasonphases" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-layer-group fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_LEAGUES');?></div>
                            <a href="index.php?option=com_footballmanager&view=leagues" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-map-marker-alt fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_LOCATIONS');?></div>
                            <a href="index.php?option=com_footballmanager&view=locations" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-hat-cowboy fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_POSITIONS');?></div>
                            <a href="index.php?option=com_footballmanager&view=positions" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-users fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_TEAMS');?></div>
                            <a href="index.php?option=com_footballmanager&view=teams" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-chalkboard-teacher fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_COACHES');?></div>
                            <a href="index.php?option=com_footballmanager&view=coaches" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-user fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_PLAYERS');?></div>
                            <a href="index.php?option=com_footballmanager&view=players" class="nxd-position-cover"></a>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-id-card-alt fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_REFEREES');?></div>
                            <a href="index.php?option=com_footballmanager&view=referees" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-handshake fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_SPONSORS');?></div>
                            <a href="index.php?option=com_footballmanager&view=sponsors" class="nxd-position-cover"></a>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <i class="fas fa-life-ring fa-4x"></i>
                            <div><?php echo Text::_('COM_FOOTBALLMANAGER_MANUAL');?></div>
                            <a href="https://www.nx-designs.ch" target="_blank" class="nxd-position-cover"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <h3>FootballManager 2.0</h3>
                <div class="version"><span class="badge bg-info p-2">Version <?php echo $this->version;?></span></div>
            </div>
        </div>

</div>