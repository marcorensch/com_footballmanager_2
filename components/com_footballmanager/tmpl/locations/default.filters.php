<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

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
