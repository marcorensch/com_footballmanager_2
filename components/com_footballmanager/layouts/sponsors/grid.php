<?php
/**
 * @package     Joomla.Site
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

// Die übergebenen Daten abrufen
$sponsors = $displayData ?? array(); // Hier greifen wir direkt auf die Sponsoren-Daten zu

$app    = Factory::getApplication();
$params = $app->getParams();

$imgSource = $params->get('sponsors_image_src', 'logo') === '0' ? false : $params->get('sponsors_image_src', 'logo'); // logo or image

$gridCols = 'uk-child-width-1-' . $params->get('sponsors_grid_columns', '3') . ' uk-child-width-1-' . $params->get('sponsors_grid_columns_s', '3') . '@s' . ' uk-child-width-1-' . $params->get('sponsors_grid_columns_m', '3') . '@m' . ' uk-child-width-1-' . $params->get('sponsors_grid_columns_l', '3') . '@l' . ' uk-child-width-1-' . $params->get('sponsors_grid_columns_xl', '3') . '@xl';
$gridSize = 'uk-grid-' . $params->get('sponsors_grid_size', 'small');
$cardStyle = 'uk-card-' . $params->get('sponsors_card_style', 'default');
?>
<div class="uk-margin">
    <div class="uk-grid uk-grid-match <?php echo $gridSize . ' ' . $gridCols;?>">
		<?php foreach ($sponsors as $sponsor) :
			$renderSponsor = $params->get('render_sponsor_links', 1) && $sponsor->website;
			$cardClassList = 'uk-card '.$cardStyle.' uk-position-relative uk-flex uk-flex-middle uk-flex-center';
			if ($renderSponsor) $cardClassList .= ' uk-card-hover';
			?>
            <div>
                <div class="<?php echo $cardClassList; ?>">
					<?php
					if ($imgSource && $sponsor->$imgSource)
					{
						echo LayoutHelper::render('joomla.html.image', ['src' => $sponsor->$imgSource, 'alt' => $sponsor->title, 'title' => $sponsor->title]);
					}
					else
					{
						echo '<div class="uk-card-body">';
						echo '<h3 class="uk-margin-remove uk-padding-remove sponsor-title">' . $sponsor->title . '</h3>';
						echo '<p>' . $sponsor->description . '</p>';
						echo '</div>';
					}
					?>
					<?php if ($renderSponsor): ?>
                        <a href="<?php echo $sponsor->website; ?>" target="_blank"
                           class="uk-position-cover sponsors-link"></a>
					<?php endif; ?>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</div>
