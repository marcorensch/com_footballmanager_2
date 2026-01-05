<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Association\AssociationExtensionHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Table\Table;
use NXD\Component\Footballmanager\Site\Helper\AssociationHelper;

/**
 * location associations helper.
 *
 * @since  2.0.0
 */
class AssociationsHelper extends AssociationExtensionHelper
{
	/**
	 * The extension name
	 *
	 * @var     array   $extension
	 *
	 * @since   2.0.0
	 */
	protected $extension = 'com_footballmanager';

	/**
	 * Array of content types
	 *
	 * @var     array   $itemTypes
	 *
	 * @since   2.0.0
	 */
	protected $itemTypes = ['location', 'category', 'group'];

	/**
	 * Has the extension association support
	 *
	 * @var     boolean   $associationsSupport
	 *
	 * @since   2.0.0
	 */
	protected $associationsSupport = true;

	/**
	 * Method to get the associations for a given content.
	 *
	 * @param   integer  $id    Id of the content
	 * @param   string   $view  Name of the view
	 *
	 * @return  array   Array of associations for the content
	 *
	 * @since  2.0.0
	 */
	public function getAssociationsForItem($id = 0, $view = null)
	{
		return AssociationHelper::getAssociations($id, $view);
	}

	/**
	 * Get the associated items for an content
	 *
	 * @param   string  $typeName  The content type
	 * @param   int     $id        The id of content for which we need the associated items
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	public function getAssociations($typeName, $id)
	{
		$type = $this->getType($typeName);

		$context    = $this->extension . '.content';
		$catidField = 'catid';

		if ($typeName === 'category') {
			$context    = 'com_categories.content';
			$catidField = '';
		}

		// Get the associations.
		$associations = Associations::getAssociations(
			$this->extension,
			$type['tables']['a'],
			$context,
			$id,
			'id',
			'alias',
			$catidField
		);

		return $associations;
	}

	/**
	 * Get content information
	 *
	 * @param   string  $typeName  The content type
	 * @param   int     $id        The id of content for which we need the associated items
	 *
	 * @return  Table|null
	 *
	 * @since   2.0.0
	 */
	public function getItem($typeName, $id)
	{
		if (empty($id)) {
			return null;
		}

		$table = null;

		switch ($typeName) {
			case 'location':
				$table = Table::getInstance('LocationTable', 'NXD\\Component\\Footballmanager\\Administrator\\Table\\');
				break;

			case 'category':
				$table = Table::getInstance('Category');
				break;
		}

		if (empty($table)) {
			return null;
		}

		$table->load($id);

		return $table;
	}

	/**
	 * Get information about the type
	 *
	 * @param   string  $typeName  The content type
	 *
	 * @return  array  Array of content types
	 *
	 * @since   2.0.0
	 */
	public function getType($typeName = '')
	{
		$fields  = $this->getFieldsTemplate();
		$tables  = [];
		$joins   = [];
		$support = $this->getSupportTemplate();
		$title   = '';

		if (in_array($typeName, $this->itemTypes)) {
			switch ($typeName) {
				case 'location':
					$fields['title'] = 'a.name';
					$fields['state'] = 'a.published';

					$support['state'] = true;
					$support['acl'] = true;
					$support['category'] = true;
					$support['save2copy'] = true;

					$tables = [
						'a' => '#__footballmanager_locations'
					];

					$title = 'location';
					break;

				case 'category':
					$fields['created_user_id'] = 'a.created_user_id';
					$fields['ordering'] = 'a.lft';
					$fields['level'] = 'a.level';
					$fields['catid'] = '';
					$fields['state'] = 'a.published';

					$support['state'] = true;
					$support['acl'] = true;
					$support['checkout'] = false;
					$support['level'] = false;

					$tables = [
						'a' => '#__categories'
					];

					$title = 'category';
					break;
			}
		}

		return [
			'fields'  => $fields,
			'support' => $support,
			'tables'  => $tables,
			'joins'   => $joins,
			'title'   => $title
		];
	}

	/**
	 * Get default values for fields array
	 *
	 * @return  array
	 *
	 * @since   2.0.0
	 */
	protected function getFieldsTemplate()
	{
		return [
			'id'                  => 'a.id',
			'title'               => 'a.title',
			'alias'               => 'a.alias',
			'ordering'            => 'a.id',
			'menutype'            => '',
			'level'               => '',
			'catid'               => 'a.catid',
			'language'            => 'a.language',
			'access'              => 'a.access',
			'state'               => 'a.state',
			'created_user_id'     => '',
			'checked_out'         => '',
			'checked_out_time'    => ''
		];
	}
}
