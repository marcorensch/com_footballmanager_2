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

use Joomla\CMS\Factory;

$params = Factory::getApplication()->getParams();

if(is_null($location)){
    return;
}
?>

<li class="partner" data-group="<?php echo implode(" ", $location->filters);?>">
	<div class="uk-grid uk-grid-small uk-flex uk-flex-middle">
		<div class="uk-width-expand">
			<?php echo $location->title;?>
		</div>
		<?php if($canSeeContactInfo):?>
			<div class="nxd-contact-link-container">
				<?php if($location->phone):?>
					<a href="tel:<?php echo htmlspecialchars($location->phone);?>">
						<span uk-icon="icon: receiver; ratio: 1.1"></span>
					</a>
				<?php endif;?>
			</div>
			<div class="nxd-contact-link-container">
				<?php if($location->email):?>
					<a href="mailto:<?php echo htmlspecialchars($location->email);?>">
						<span uk-icon="icon: mail; ratio: 1.1"></span>
					</a>
				<?php endif;?>
			</div>
			<div class="nxd-contact-link-container">
				<?php if($location->web):?>
					<a href="<?php echo htmlspecialchars($location->web);?>" target="_blank">
						<span uk-icon="icon: world; ratio: 1.1"></span>
					</a>
				<?php endif;?>
			</div>
		<?php endif;?>
	</div>
</li>
