<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_hello
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class SponsorsModel extends BaseDatabaseModel
{

	protected $_items = null;

	public function getSponsors($ids = null): array|bool
	{
		$app = Factory::getApplication();

		if($this->_items === null)
		{
			$this->_items = array();
		}

		try{
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
			$now = Factory::getDate()->toSql();

			$query->select('*')
				->from($db->quoteName('#__footballmanager_sponsors', 'a'))
				->where($db->quoteName('a.published') . ' = 1')
				->where('(' . $db->quoteName('a.publish_up') . ' IS NULL OR ' . $db->quoteName('a.publish_up') . ' <= ' . $db->quote($now) . ')')
				->where('(' . $db->quoteName('a.publish_down') . ' IS NULL OR ' . $db->quoteName('a.publish_down') . ' >= ' . $db->quote($now) . ')');
			if($ids !== null){
				$idString = implode(',', $ids);
				$query->where($db->quoteName('a.id') . ' IN ' . '(' . $idString . ')');
			}

			$db->setQuery($query);
			$data = $db->loadObjectList();

			$this->_items = $data;
		}
		catch(\Exception $e)
		{
			$this->setError($e->getMessage());
			$this->_items = false;
		}

		return $this->_items;
	}

	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('params', $app->getParams());
	}
}