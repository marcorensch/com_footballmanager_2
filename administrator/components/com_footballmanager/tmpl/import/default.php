<?php

/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;
$app   = Factory::getApplication();
$input = $app->input;

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$isModal = $input->get('layout') === 'modal';

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

$layout = 'import';
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

$title = 'Import';

?>

<form action="<?php echo Route::_($formURL); ?>" method="post" name="adminForm" id="adminForm" class="form-vertical">

    <div class="px-4 py-5 my-5 text-center">
        <span class="fa-8x mb-4 fas fa-file-import" aria-hidden="true"></span>
        <h1 class="display-5 fw-bold"><?php echo $title; ?></h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">
	            <?php echo $this->getForm()->renderField('upload'); ?>
            </p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
				<?php if (true) : ?>
                    <button id="confirmButton" type="submit" class="btn btn-primary btn-lg px-4 me-sm-3">Upload</button>
                    <button onclick="history.back()">back</button>
				<?php endif; ?>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
