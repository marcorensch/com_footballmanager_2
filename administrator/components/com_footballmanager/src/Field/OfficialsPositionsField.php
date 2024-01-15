<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\Footballmanager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\SubformField;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\Database\DatabaseInterface;
use SimpleXMLElement;

defined('_JEXEC') or die;

class OfficialsPositionsField extends SubformField {

	protected $type = 'OfficialsPositions';

	/**
	 * @throws \Exception
	 */
	public function loadSubForm()
	{
		$positions = $this->getPositionsForOfficials($this->getCategoryIdForOfficials());
		$control = $this->name;

		// Prepare the form template
		$formname = 'subform.' . str_replace(['jform[', '[', ']'], ['', '.', ''], $this->name);
//		$form     = Form::getInstance($formname, $this->formsource, ['control' => $control]);
		$form = Factory::getContainer()->get(FormFactoryInterface::class)->createForm($formname, ['control' => $control]);
		$form->load('<?xml version="1.0" encoding="utf-8"?><form> </form>');

		// Add the fields to the form.
		foreach ($positions as $position){
			$xml = new SimpleXMLElement('<field name="custom_official_'.$position->id.'" type="officials" layout="joomla.form.field.list-fancy-select" label="'.$position->title.'" />');
			// Add the field to the form
			$form->setField($xml, $group = null, $replace = true, $fieldset = 'default');
		}

		return $form;
	}

	protected function getCategoryIdForOfficials()
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__categories');
		$query->where('alias = ' . $db->quote('official'));
		$db->setQuery($query);
		$categoryId = $db->loadResult();
		return $categoryId;
	}

	protected function getPositionsForOfficials($categoryId)
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__footballmanager_positions');
		$query->where('catid = ' . $categoryId);
		$query->order('ordering ASC');
		$db->setQuery($query);
		$positions = $db->loadObjectList();

		return $positions;
	}
}