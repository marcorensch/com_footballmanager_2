<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

// Die übergebenen Daten abrufen
$displayData = $displayData ?? array();
$sponsors    = $displayData['sponsors'] ?? array(); // Hier greifen wir direkt auf die Sponsoren-Daten zu

$app    = Factory::getApplication();
$params = $app->getParams();
?>

<h2 class="sponsored">Sponsored by</h2>
<div class="uk-grid uk-grid-small uk-child-width-1-3">
	<?php foreach ($sponsors as $sponsor) :
        $renderSponsor = $params->get('render_sponsor_links', 1) && $sponsor->website;
		$cardClassList = 'uk-card uk-card-default uk-text-center uk-position-relative';
        if($renderSponsor) $cardClassList .= ' uk-card-hover';
        ?>
        <div>
            <div class="<?php echo $cardClassList;?>">
                <div class="uk-card-body">
                    <h3 class="uk-margin-remove uk-padding-remove sponsor-title"><?php echo $sponsor->title; ?></h3>
                    <p><?php echo $sponsor->description; ?></p>
                </div>
	            <?php if ($renderSponsor):?>
                    <a href="<?php echo $sponsor->website; ?>" target="_blank" class="uk-position-cover sponsors-link"></a>
	            <?php endif; ?>
            </div>
        </div>
	<?php endforeach; ?>
</div>
