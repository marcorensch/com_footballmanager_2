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

$listLayout = $params->get('sponsors_list_style', 'divider');
$classList  = 'uk-list uk-list-' . $listLayout . '';
?>

<h2 class="sponsored">Sponsored by</h2>
<ul class="<?php echo $classList; ?>">
	<?php foreach ($sponsors as $sponsor) : ?>
        <li class="uk-position-relative">
            <div >
                <span class="sponsor-title"><?php echo $sponsor->title; ?></span>
				<?php if ($params->get('render_sponsor_links', 1) && $sponsor->website):?>
                    <a href="<?php echo $sponsor->website; ?>" target="_blank" class="uk-position-cover sponsors-link"></a>
				<?php endif; ?>
            </div>
        </li>
	<?php endforeach; ?>
</ul>
