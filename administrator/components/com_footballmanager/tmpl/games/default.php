<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');
$wa->addInlineScript("
    jQuery(document).ready(function(){
        jQuery('#toolbar-download').on('click', function(e){
            const taskField = document.querySelectorAll('[name=\"task\"]');
            taskField[0].value = '';
        });
    });");

$wa->addInlineStyle('
    .past-game-row > * {
        background-color: #f5f5f5 !important;
        & > * {
         opacity: 0.9;
         }
    }'
);

$canChange = true;
$assoc     = Associations::isEnabled();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder === 'a.ordering';

if ($saveOrder && !empty($this->items))
{
	$saveOrderingUrl = 'index.php?option=com_footballmanager&task=games.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

$params = ComponentHelper::getParams('com_footballmanager');
if($params->get('show_filters_by_default', 0)){
	$showFiltersScript= <<<JS
    document.addEventListener("DOMContentLoaded", ()=>{
        const filtersContainer = document.querySelector(".js-stools-container-filters");
        //add active Class
        filtersContainer.classList.add("js-stools-container-filters-visible");
    })
    JS;
	$wa->addInlineScript($showFiltersScript);
}

?>

    <form action="<?php echo Route::_('index.php?option=com_footballmanager&view=games'); ?>" method="post"
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
                        <table class="table gamesList" id="gamesList">
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

                                <th scope="col" style="min-width:130px" class="d-none d-md-table-cell">
                                </th>

                                <th scope="col" style="min-width:10%" class="">
		                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_KICKOFF', 'a.kickoff', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:150px">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_TEAM_HOME', 'ht.title', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:150px">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_TEAM_AWAY', 'at.title', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_SEASON', 'season_name', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_SEASON_PHASE', 'season_phase_name', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_LEAGUE', 'league_name', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_LOCATION', 'l.title', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
		                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_FOOTBALLMANAGER_TABLEHEAD_CATEGORY', 'c.title', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'a.created_by_username', $listDirn, $listOrder); ?>
                                </th>

                                <th scope="col" style="min-width:1%">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder) ?>
                                </th>

                                <th scope="col" style="min-width:1%">
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
                                $pastGameCls = $item->kickoff < date('Y-m-d H:i:s') ? 'past-game-row' : '';
								?>
                                <tr class="row<?php echo $i % 2; ?> <?php echo $pastGameCls;?>" data-draggable-group="playerItems">
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
										echo HTMLHelper::_('jgrid.published', $item->published, $i, 'games.', $canChange, 'cb');
										?>
                                    </td>
                                    <th scope="row" class="has-context">
                                        <div style="display:none">
											<?php echo $this->escape($item->lastname) . ', ' . $this->escape($item->firstname); ?>
                                        </div>
                                        <a class="hasTooltip btn btn-primary btn-sm"
                                           href="<?php echo Route::_('index.php?option=com_footballmanager&task=game.edit&id=' . (int) $item->id); ?>"
                                           title="<?php echo Text::_('JACTION_EDIT'); ?>">
											<?php echo Text::_('COM_FOOTBALLMANAGER_EDIT_GAME_BTN_LABEL') ?>
                                        </a>
                                    </th>

                                    <td>
                                        <span class="kickoff-date"><?php echo HTMLHelper::_('date', $item->kickoff, Text::_('DATE_FORMAT_LC5')); ?></span><br>
                                    </td>

                                    <td>
                                        <span class="text-nowrap"><?php echo $item->home_team_name; ?></span>
                                    </td>

                                    <td>
                                        <span class="text-nowrap"><?php echo $item->away_team_name; ?></span>
                                    </td>

                                    <td>
                                        <span class="text-nowrap"><?php echo $item->season_name; ?></span>
                                    </td>
                                    <td>
                                        <span class="text-nowrap"><?php echo $item->season_phase_name; ?></span>
                                    </td>
                                    <td>
                                        <span class="text-nowrap"><?php echo $item->league_name; ?></span>
                                    </td>

                                    <td>
                                        <span class="text-nowrap"><?php echo $item->location_name; ?></span>
                                    </td>

                                    <td>
                                        <span class="category text-nowrap"><?php echo $item->category_title; ?></span>
                                    </td>

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
// Load the Import Modal Layout
$data = array(
	'form' => $this->importform,
	'task' => 'players.import',
);

echo LayoutHelper::render('admin.importmodal', $data);

