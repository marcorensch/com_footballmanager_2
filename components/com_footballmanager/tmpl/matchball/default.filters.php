<?php

/**
 * * @package     NXD.FootballManager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$params = Factory::getApplication()->getParams();

$filterItemsClassnames = '';
$activeAlreadySet      = false;
$active = "";

?>

<?php if($params->get('show_all_button','1')):
	$activeAlreadySet = true;
	?>
	<li class="uk-active" uk-filter-control>
        <a href="#"><?php echo Text::_('COM_NXDCP_SITE_LABEL_ALL'); ?></a>
    </li>
<?php endif; ?>
<?php
foreach($this->groups as $key => $group) :
	if(!$activeAlreadySet){
		$active = ' uk-active';
		$activeAlreadySet = true;
	}
	$isHidden = $group->itemsCount === 0 && $params->get('hide_empty_groups') ? 'hidden': ''; ?>
	<li <?php echo $isHidden;?> class="<?php echo $filterItemsClassnames . $active;?>" uk-filter-control="[data-group*='<?php echo $group->alias; ?>']">
		<a href="#">
			<?php echo htmlspecialchars($group->title); ?>
		</a>
	</li>
	<?php $active = ''; ?>
<?php endforeach; ?>
