<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Layout file for import modal
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();
$doc = $app->getDocument();
$wa = $doc->getWebAssetManager();

$wa->addInlineScript(<<<JS
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
        const modal = document.querySelector('#import-modal');
        const fileInputField = document.querySelector('#import-modal input[type=\"file\"]');
        
        
        // Attach an event listener on modal shown
        modal.addEventListener('show.bs.modal', event => {
           fileInputField.value = '';
           submitButton.disabled = true;
        });

       
        // Add 'modal-dialog-centered' class to the modal container
        const modalContainer = document.querySelector('#import-modal .modal-dialog');
        modalContainer.classList.add('modal-dialog-centered');
        
        // Enable / Disable Submit Button based on file input
        const submitButton = document.querySelector('#import-submit');
        submitButton.disabled = true;
        fileInputField.addEventListener('change', function(e) {
            if (fileInputField.value) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        });
        
      });
JS);

$wa->addInlineStyle("
    .modal-dialog.jviewport-width50 {
        min-width: 400px;
    }
    .jviewport-height40 {
        min-height: 450px;
    }
  ");

$displayData = $displayData ?? array();
$form = $displayData['form'];       // JForm object
$task = $displayData['task'];       // 'controller.method'

if( !$form || !$task ) {
	$app->enqueueMessage(Text::_('Could not build Import Modal'), 'error');
	return;
}

$actionUri = Route::_('index.php?option=com_footballmanager&task=' . $task);

$modalParams = array(
	'title'       => Text::_('COM_FOOTBALLMANAGER_IMPORT') . ' ' . Text::_('COM_FOOTBALLMANAGER_SEASONS'),
	'closeButton' => true,
	'height'      => '500px',
	'width'       => '600px',
	'backdrop'    => 'static',
	'keyboard'    => true,
	'modalWidth'  => 50,
	'bodyHeight'  => 40,
	'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCANCEL') . '</button>' . '<button onclick="document.getElementById(\'uploadForm\').submit()" id="import-submit" class="btn btn-success">' . Text::_('COM_FOOTBALLMANAGER_UPLOAD_START') . '</button>',
);
$modalBody   = '<form action="' . $actionUri . '" method="post" enctype="multipart/form-data" name="uploadForm" id="uploadForm" class="p-4">' .
	JHtml::_('form.token') .
	'<div>' .
        $form->renderField('forced_import') .
        $form->renderField('linked_elements') .
        $form->renderField('upload_file') .
	'</div>' .
	'</form>';

echo HTMLHelper::_('bootstrap.renderModal', 'import-modal', $modalParams, $modalBody);


