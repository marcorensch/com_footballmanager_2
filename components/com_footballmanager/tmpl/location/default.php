<?php

/**
 * @package     Joomla.Site
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

//if ($this->content->params->get('show_name')) {
//
//	if ($this->params->get('show_locations_name_label')) {
//		echo Text::_('COM_FOOTBALLMANAGER_NAME');
//	}
//
//	echo $this->content->title;
//}

$app    = Factory::getApplication();
$params = $app->getParams();
$sponsorsLayout = $params->get('sponsors_layout', 'sponsors.grid');

echo '<pre>' . var_export($params, 1) . '</pre>';

$itemImage = HTMLHelper::_('cleanImageURL', $this->item->image);
$cleanImgUrl = $this->escape($itemImage->url);

$wa = $this->document->getWebAssetManager();

if ($params->get('load_uikit', 1))
{
	$wa->useStyle('com_footballmanager.uikitcss');
	$wa->useScript('com_footballmanager.uikitjs');
}

$wa->useStyle('com_footballmanager.main');

$wa->addInlineStyle('
    .nxd-location-header {
        background: url("/'.$cleanImgUrl.'");
        background-size: cover;
        background-position: center;
        padding:0;
    }
    .nxd-backdrop-blurred{
        backdrop-filter: blur(5px);
    }
    ');



?>

    <div class="uk-section uk-section-secondary uk-section-small nxd-location-header">
        <div class="uk-container uk-position-relative uk-padding uk-overlay-primary nxd-backdrop-blurred">
            <h1 class="uk-heading-small uk-text-bold uk-margin-remove element-header"><?php echo htmlspecialchars($this->item->title); ?></h1>
            <?php if($this->item->city):?>
            <div class="uk-text-lead element-header-city"><?php echo htmlspecialchars($this->item->city); ?></div>
            <?php endif; ?>

			<?php if ($this->item->event->afterDisplayTitle): ?>
                <div class="fields-after-display-title-container">
					<?php echo $this->item->event->afterDisplayTitle; ?>
                </div>
			<?php endif; ?>
        </div>
    </div>

<?php //echo LayoutHelper::render('joomla.html.image', ['src' => $this->item->image, 'alt' => $this->item->title, 'class' => 'uk-cover']); ?>

<?php if ($this->item->event->beforeDisplayContent): ?>
    <div class="uk-section uk-section-small fields-before-display-content">
        <div class="uk-container">
			<?php echo $this->item->event->beforeDisplayContent; ?>
        </div>
    </div>
<?php endif; ?>

    <div class="uk-section uk-section-small">
        <div class="uk-container">
            <?php echo HTMLHelper::_('content.prepare', $this->item->description); ?>
        </div>
    </div>

<?php if ($this->item->event->afterDisplayContent): ?>
    <div class="uk-section uk-section-small fields-after-display-content">
        <div class="uk-container">
			<?php echo $this->item->event->afterDisplayContent; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($params->get('location_show_sponsors', 1) && $this->item->sponsors): ?>
    <div class="uk-section uk-section-small sponsors-section">
        <div class="uk-container">
			<?php
            echo '<h2>' . Text::_('COM_FOOTBALLMANAGER_SPONSORED_BY') . '</h2>';
			echo LayoutHelper::render($sponsorsLayout, $this->item->sponsors); // Rendere das Layout
            ?>
        </div>
    </div>
<?php endif; ?>

<?php echo '<pre>' . var_export($this->item, 1) . '</pre>'; ?>
