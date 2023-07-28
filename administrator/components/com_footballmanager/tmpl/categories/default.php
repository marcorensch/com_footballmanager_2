<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;
//echo LayoutHelper::render('admin.submenu', null); // Rendere das Layout
?>

<div class="com_cpanel">
    <div class="cpanel-modules">
        <div class="card-columns">
			<?php foreach ($this->sections as $item): ?>
                <div class="col">
                    <div class="card">
                        <h2 class="card-header">
                            <i class="fas fa-<?php echo $item->icon; ?>" aria-hidden="true"></i>
                            <span style="margin-left: 10px;"><?php echo $item->title; ?></span>
                        </h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <a href="<?php echo $item->categoriesdUri; ?>">Categories</a>
                                <span class="menu-quicktask">
                                <a href="<?php echo $item->addCategoryUri; ?>">
                                    <span class="icon-plus" title="COM_FOOTBALLMANAGER_NEW_CATEGORY"
                                          aria-hidden="true"></span>
                                    <span class="visually-hidden">New <?php echo $item->title; ?> Category</span>
                                </a>
                            </span>
                            </li>
                        </ul>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
    </div>
</div>