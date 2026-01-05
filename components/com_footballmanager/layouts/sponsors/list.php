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

$document = Factory::getDocument();



// Die übergebenen Daten abrufen
$sponsors    = $displayData ?? array(); // Hier greifen wir direkt auf die Sponsoren-Daten zu

$app    = Factory::getApplication();
$document = $app->getDocument();
$wa = $document->getWebAssetManager();
$params = $app->getParams();

$wa->useStyle('com_footballmanager.sponsorList');

$imgSource = $params->get('sponsors_image_src', 'logo') === '0' ? false : $params->get('sponsors_image_src', 'logo'); // logo or image
$listLayout = $params->get('sponsors_list_style', 'divider');
$classList  = 'uk-list uk-list-' . $listLayout . '';

?>
<div class="uk-margin">
    <ul class="<?php echo $classList; ?>">
		<?php foreach ($sponsors as $sponsor) : ?>
            <li class="uk-position-relative">
                <div>
					<?php
					if($imgSource){
						if($sponsor->$imgSource){
							echo LayoutHelper::render('joomla.html.image', ['src' => $sponsor->$imgSource, 'alt' => $sponsor->title, 'title' => $sponsor->title, 'class' => 'sponsor-list-image']);
						}else{
							echo '<span class="sponsor-image-placeholder"></span>';
						}
					}
					?>
                    <span class="sponsor-title"><?php echo $sponsor->title; ?></span>
					<?php if ($params->get('render_sponsor_links', 1) && $sponsor->website):?>
                        <a href="<?php echo $sponsor->website; ?>" target="_blank" class="uk-position-cover sponsors-link"></a>
					<?php endif; ?>
                </div>
            </li>
		<?php endforeach; ?>
    </ul>
</div>

