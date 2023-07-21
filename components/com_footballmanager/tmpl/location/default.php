<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

//if ($this->item->params->get('show_name')) {
//
//	if ($this->params->get('show_locations_name_label')) {
//		echo Text::_('COM_FOOTBALLMANAGER_NAME');
//	}
//
//	echo $this->item->title;
//}

$app    = Factory::getApplication();
$params = $app->getParams();

echo '<pre>' . var_export($params, 1) . '</pre>';

$wa = $this->document->getWebAssetManager();

if ($params->get('load_uikit', 1))
{
	$wa->useStyle('com_footballmanager.uikitcss');
	$wa->useScript('com_footballmanager.uikitjs');
}

$wa->useStyle('com_footballmanager.main');

?>

    <div class="uk-section uk-section-primary uk-section-small">
        <div class="uk-container">
            <h1><?php echo $this->item->title; ?></h1>
			<?php if ($this->item->event->afterDisplayTitle): ?>
                <div class="fields-after-display-title-container">
					<?php echo $this->item->event->afterDisplayTitle; ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
<?php if ($this->item->event->beforeDisplayContent): ?>
    <div class="uk-section uk-section-small fields-before-display-content">
        <div class="uk-container">
			<?php echo $this->item->event->beforeDisplayContent; ?>
        </div>
    </div>
<?php endif; ?>

    <div class="uk-section uk-section-small">
        <div class="uk-container">
            CONTENT
        </div>
    </div>

<?php if ($this->item->event->afterDisplayContent): ?>
    <div class="uk-section uk-section-small fields-after-display-content">
        <div class="uk-container">
			<?php echo $this->item->event->afterDisplayContent; ?>
        </div>
    </div>
<?php endif; ?>