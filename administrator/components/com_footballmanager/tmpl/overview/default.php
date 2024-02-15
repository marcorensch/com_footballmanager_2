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
    height: 100%; // Grid Match
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
.nxd-position-relative{
    position: relative;
}
.nxd-card-footer{
margin-top:10px;
}
'
);

$widgets = array(
    'com_footballmanager.seasons' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_SEASONS'),
        'icon' => 'fas fa-trophy fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=seasons',
        'category' => null,
    ),
    'com_footballmanager.seasonphases' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_SEASON_PHASES'),
        'icon' => 'fas fa-angle-double-right fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=seasonphases',
        'category' => null,
    ),
    'com_footballmanager.leagues' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_LEAGUES'),
        'icon' => 'fas fa-layer-group fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=leagues',
        'category' => null,
    ),
    'com_footballmanager.countries' => array(
	    'title' => Text::_('COM_FOOTBALLMANAGER_COUNTRIES'),
	    'icon' => 'fas fa-globe fa-4x',
	    'link' => 'index.php?option=com_footballmanager&view=countries',
	    'category' => 'com_footballmanager.countries',
    ),
    'com_footballmanager.locations' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_LOCATIONS'),
        'icon' => 'fas fa-map-marker-alt fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=locations',
        'category' => 'com_footballmanager.locations',
    ),
    'com_footballmanager.positions' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_POSITIONS'),
        'icon' => 'fas fa-hat-cowboy fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=positions',
        'category' => null,
    ),
    'com_footballmanager.teams' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_TEAMS'),
        'icon' => 'fas fa-users fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=teams',
        'category' => 'com_footballmanager.teams',
    ),
    'com_footballmanager.games' => array(
	    'title' => Text::_('COM_FOOTBALLMANAGER_GAMES'),
	    'icon' => 'fas fa-football-ball fa-4x',
	    'link' => 'index.php?option=com_footballmanager&view=games',
	    'category' => 'com_footballmanager.games',
    ),
    'com_footballmanager.coaches' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_COACHES'),
        'icon' => 'fas fa-chalkboard-teacher fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=coaches',
        'category' => 'com_footballmanager.coaches',
    ),
    'com_footballmanager.players' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_PLAYERS'),
        'icon' => 'fas fa-user fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=players',
        'category' => 'com_footballmanager.players',
    ),
    'com_footballmanager.cheerleaders' => array(
	    'title' => Text::_('COM_FOOTBALLMANAGER_CHEERLEADERS'),
	    'icon' => 'fas fa-smile fa-4x',
	    'link' => 'index.php?option=com_footballmanager&view=cheerleaders',
	    'category' => 'com_footballmanager.cheerleaders',
    ),
    'com_footballmanager.officials' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_OFFICIALS'),
        'icon' => 'fas fa-id-card-alt fa-4x',
        'link' => 'index.php?option=com_footballmanager&view=officials',
        'category' => 'com_footballmanager.officials',
    ),
    'com_footballmanager.sponsors' => array(
	    'title' => Text::_('COM_FOOTBALLMANAGER_SPONSORS'),
	    'icon' => 'fas fa-handshake fa-4x',
	    'link' => 'index.php?option=com_footballmanager&view=sponsors',
	    'category' => 'com_footballmanager.sponsors',
    ),
//    'com_footballmanager.categories' => array(
//        'title' => Text::_('JCATEGORIES'),
//        'icon' => 'fas fa-folder fa-4x',
//        'link' => 'index.php?option=com_footballmanager&view=categories',
//        'category' => null,
//    ),
    'com_footballmanager.manual' => array(
        'title' => Text::_('COM_FOOTBALLMANAGER_MANUAL'),
        'icon' => 'fas fa-life-ring fa-4x',
        'link' => 'https://www.nx-designs.ch',
        'category' => null,
    ),
)

?>

<div class="">
    <div class="nxd-overview">
        <div class="row g-8">
            <div class="col-lg-9">
                <div class="row g-2">
                    <?php foreach ($widgets as $widget):?>
                    <div class="col-lg-3">
                        <div class="nxd-overview-item text-center p-4">
                            <div class="nxd-position-relative">
                            <i class="<?php echo $widget['icon']?>"></i>
                            <div><?php echo $widget['title']?></div>
                            <a href="<?php echo $widget['link']?>" class="nxd-position-cover"></a>
                            </div>
                            <div class="nxd-card-footer">
                                <?php if($widget['category']):?>
                                <a href="index.php?option=com_categories&extension=<?php echo $widget['category']?>" class="btn btn-primary">
                                    <?php echo Text::_('JCATEGORIES');?>
                                </a>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;?>

                </div>
            </div>
            <div class="col-lg-3">
                <h3>FootballManager 2.0</h3>
                <div class="version"><span class="badge bg-info p-2">Version <?php echo $this->version;?></span></div>
            </div>
        </div>

</div>