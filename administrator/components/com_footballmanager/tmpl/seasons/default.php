<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');
$wa->addInlineScript("
    document.addEventListener('DOMContentLoaded', function() {
    
        // EXPORT / DOWNLOAD
        // Get the download button element
        const downloadButton = document.getElementById('toolbar-download');
        // Attach a click event listener to the download button
        downloadButton.addEventListener('click', function(e) {
            // Get all elements with the name attribute equal to task
            const taskField = document.querySelectorAll('[name=\"task\"]');
            // Update the value of the first taskField element to an empty string
            taskField[0].value = '';
        });
    
        // IMPORT / UPLOAD
        // Get the modal container element
        const modalContainer = document.querySelector('#import-modal .modal-dialog');
    
        // Add 'modal-dialog-centered' class to the modal container
        modalContainer.classList.add('modal-dialog-centered');
      });
  
  ");

$canChange = true;
$assoc     = Associations::isEnabled();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder === 'a.ordering';

if ($saveOrder && !empty($this->items))
{
	$saveOrderingUrl = 'index.php?option=com_footballmanager&task=leagues.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

?>

<?php $editIcon = '<span class="fa fa-pencil-square mr-2" aria-hidden="true"></span>'; ?>

    <form action="<?php echo Route::_('index.php?option=com_footballmanager&view=seasons'); ?>" method="post"
          name="adminForm" id="adminForm">
        <div class="row">
            <div class="col-md-12">
                <div id="j-main-container" class="j-main-container">
					<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
					<?php if (empty($this->items)) : ?>
                        <div class="alert alert-warning">
							<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                        </div>
					<?php else : ?>
                        <table class="table seasonList" id="seasonsList">
                            <caption class="visually-hidden">
								<?php echo Text::_('COM_FOOTBALLMANAGER_TABLE_CAPTION'); ?>,
                                <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                                <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                            </caption>
                            <thead>
                            <tr>
                                <td style="width:1%" class="text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>

                                <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                                </th>

                                <th scope="col" style="width: 1%; min-width: 85px" class="text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder) ?>
                                </th>

                                <th scope="col" style="min-width:150px" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_NAME', 'a.title', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'a.created_by_username', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width: 10%" class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_CREATEDON', 'a.created_at', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder) ?>
                                </th>

                                <th scope="col" style="width:5%">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder) ?>
                                </th>
                            </tr>
                            </thead>
                            <tbody <?php if ($saveOrder) :
								?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php
							endif; ?>>
							<?php
							$n = count($this->items);
							foreach ($this->items as $i => $item) :
								$ordering = ($listOrder == 'ordering');
								?>
                                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="seasonItems">
                                    <td class="text-center">
										<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
										<?php
										$iconClass = '';
										if (!$canChange)
										{
											$iconClass = ' inactive';
										}
                                        elseif (!$saveOrder)
										{
											$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
										}
										?>
                                        <span class="sortable-handler <?php echo $iconClass ?>">
                                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                        </span>
										<?php if ($canChange && $saveOrder) : ?>
                                            <input type="text" name="order[]" size="5"
                                                   value="<?php echo $item->ordering; ?>"
                                                   class="width-20 text-area-order hidden">
										<?php endif; ?>
                                    </td>

                                    <td class="text-center">
										<?php
										echo HTMLHelper::_('jgrid.published', $item->published, $i, 'seasons.', $canChange, 'cb');
										?>
                                    </td>
                                    <th scope="row" class="has-context">
                                        <div style="display:none">
											<?php echo $this->escape($item->title); ?>
                                        </div>
                                        <a class="hasTooltip"
                                           href="<?php echo Route::_('index.php?option=com_footballmanager&task=season.edit&id=' . (int) $item->id); ?>"
                                           title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape(addslashes($item->title)); ?>">
											<?php echo $editIcon; ?><?php echo $this->escape($item->title); ?>
                                        </a>
                                    </th>
                                    <td class="small d-none d-md-table-cell">
										<?php if ((int) $item->created_by > 0) : ?>
                                            <a
                                                    href="<?php echo Route::_('index.php?option=com_users&task=user.edit&id=' . (int) $item->created_by); ?>"
                                                    title="<?php echo Text::_('JAUTHOR'); ?>"
                                                    target="_blank">
												<?php echo $item->author_name; ?>
                                            </a>
										<?php else : ?>
											<?php echo Text::_('JNONE'); ?>
										<?php endif; ?>
                                    </td>
                                    <td class="small text-center d-none d-md-table-cell">
										<?php echo HTMLHelper::_('date', $item->created_at, Text::_('DATE_FORMAT_LC4')); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell">
										<?php echo $item->access_level; ?>
                                    </td>

                                    <td class="d-none d-md-table-cell">
										<?php echo $item->id; ?>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                            </tbody>
                        </table>
						<?php echo $this->pagination->getListFooter(); ?>
					<?php endif; ?>
                    <input type="hidden" name="task" value="">
                    <input type="hidden" name="boxchecked" value="0">
					<?php echo HTMLHelper::_('form.token'); ?>
                </div>
            </div>
        </div>
    </form>

<?php


$modalParams = array(
	'title'       => Text::_('COM_FOOTBALLMANAGER_IMPORT') . ' ' . Text::_('COM_FOOTBALLMANAGER_SEASONS'),
	'closeButton' => true,
	'height'      => '500px',
	'width'       => '300px',
	'backdrop'    => 'static',
	'keyboard'    => true,
	'modalWidth'  => 30,
	'bodyHeight'  => 30,
	'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCANCEL') . '</button>' . '<button onclick="document.getElementById(\'uploadForm\').submit()" class="btn btn-success">' . Text::_('COM_FOOTBALLMANAGER_UPLOAD_START') . '</button>',
);
$modalBody   = '<form action="' . Route::_('index.php?option=com_footballmanager&task=seasons.import') . '" method="post" enctype="multipart/form-data" name="uploadForm" id="uploadForm" class="p-4">' .
	JHtml::_('form.token') .
    '<div>' .
	$this->importform->renderField('forced_import') .
	$this->importform->renderField('linked_elements') .
	$this->importform->renderField('upload_file') .
	'</div>' .
	'</form>';

echo HTMLHelper::_('bootstrap.renderModal', 'import-modal', $modalParams, $modalBody);