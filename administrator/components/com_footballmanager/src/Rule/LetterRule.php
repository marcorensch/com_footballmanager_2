<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Footballmanager\Administrator\Rule;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Form\FormRule;

/**
 * Form Rule class for the Joomla Platform.
 *
 * @since  __DEPLOY_VERSION__
 */
class LetterRule extends FormRule
{
    /**
     * The regular expression to use in testing a form field value.
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    protected $regex = '^([a-z]+)$';

    /**
     * The regular expression modifiers to use when testing a form field value.
     *
     * @var    string
     * @since  __DEPLOY_VERSION__
     */
    protected $modifiers = 'i';
}
