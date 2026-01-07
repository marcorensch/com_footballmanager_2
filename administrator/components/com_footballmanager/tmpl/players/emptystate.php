<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
    'textPrefix' => 'COM_FOOTBALLMANAGER_PLAYERS',
    'formURL' => 'index.php?option=com_footballmanager',
    'helpURL' => 'https://manuals.nx-designs.com/',
    'icon' => 'icon-copy',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.create', 'com_footballmanager') || count($user->getAuthorisedCategories('com_footballmanager', 'core.create')) > 0) {
    $displayData['createURL'] = 'index.php?option=com_footballmanager&task=player.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
