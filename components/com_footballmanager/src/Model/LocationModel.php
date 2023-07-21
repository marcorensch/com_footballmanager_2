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

class LocationModel extends BaseDatabaseModel
{

	protected $_item = null;
	private $sponsorIds = array();

	public function getLocation($pk = null): object|bool
	{
		$app = Factory::getApplication();
		$pk  = $app->input->getInt('id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db    = $this->getDatabase();
				$query = $db->getQuery(true);

				$query->select('*')
					->from($db->quoteName('#__footballmanager_locations', 'a'))
					->where($db->quoteName('a.id') . ' = ' . $db->quote($pk));

				$db->setQuery($query);
				$data = $db->loadObject();

				if (empty($data))
				{
					throw new \Exception(Text::_('COM_FOOTBALLMANAGER_LOCATION_NOT_FOUND'), 404);
				}

				$this->_item[$pk] = $data;
			}
			catch (\Exception $e)
			{
				$this->setError($e->getMessage());
				$this->_item[$pk] = false;
			}
		}

		$this->sponsorIds = $this->setSponsorIdsArray($this->_item[$pk]->sponsors);


		$sponsorsModel         = new SponsorsModel();
		$this->_item[$pk]->sponsors = $sponsorsModel->getSponsors($this->sponsorIds);

		return $this->_item[$pk];
	}

	private function setSponsorIdsArray($data): array
	{
		$values = array();
		$ids    = strlen($data) ? json_decode($data, true) : array();

		foreach ($ids as $sponsorId)
		{
			$values[] = $sponsorId['sponsor'];
		}

		return array_unique($values);
	}
}