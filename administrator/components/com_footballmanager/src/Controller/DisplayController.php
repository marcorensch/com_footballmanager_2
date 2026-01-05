<?php
/**
 * @package     com_footballmanager
 * 
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Controller;
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * The Company locations display controller.
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController{
    /**
     * The default view for the display method.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'locations';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return   BaseController | bool  This object to support chaining.
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 */
	public function display($cachable = false, $urlparams = array()){
        parent::display($cachable, $urlparams);
    }
}