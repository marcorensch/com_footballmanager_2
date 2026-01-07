<?php
/**
 * @package     NXD\Component\Footballmanager\Site\Helper
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace NXD\Component\Footballmanager\Site\Helper;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

class ManagerHelper
{
	public static function renderLink(string $url, string $text = null, array $attributes = []): string
	{
		// Is it an email?
		if (filter_var($url, FILTER_VALIDATE_EMAIL)) {
			if($attributes['subject']){
				$url .= '?subject=' . rawurlencode($attributes['subject']);
				unset($attributes['subject']);
			}
			// E-Mail Link
			return HTMLHelper::_('link', 'mailto:' . $url, $text, $attributes);
		}

		// Is it an external URL?
		if (str_starts_with($url, 'http') || str_starts_with($url, '//')) {
			// External link
			return HTMLHelper::_('link', $url, $text, $attributes);
		}

		// Otherwise internal Joomla URL
		return HTMLHelper::_('link', Route::_($url), $text, $attributes);
	}

	public static function isMailAddress(string $url): bool
	{
		return filter_var($url, FILTER_VALIDATE_EMAIL) !== false;
	}
}