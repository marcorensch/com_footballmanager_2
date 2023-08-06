<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_footballmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


require_once JPATH_ADMINISTRATOR . '/components/com_footballmanager/lib/vendor/autoload.php';

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use NXD\Component\Footballmanager\Administrator\Extension\FootballmanagerComponent;
use NXD\Component\Footballmanager\Administrator\Helper\AssociationsHelper;
use Joomla\CMS\Association\AssociationExtensionInterface;


/**
 * The Football Manager service provider.
 * https://github.com/joomla/joomla-cms/pull/20217
 *
 * @since  1.0.0
 */

return new class implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     * @param Container $container The DI container.
     * @return  void
     * @since   1.0.0
     */

    public function register(Container $container)
    {
	    $container->set(AssociationExtensionInterface::class, new AssociationsHelper);
        $container->registerServiceProvider(new CategoryFactory('\\NXD\\Component\\Footballmanager'));
        $container->registerServiceProvider(new MVCFactory('\\NXD\\Component\\Footballmanager'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\NXD\\Component\\Footballmanager'));
	    $container->registerServiceProvider(new RouterFactory('\\NXD\\Component\\Footballmanager'));

	    $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new FootballmanagerComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				$component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
	            $component->setRouterFactory($container->get(RouterFactoryInterface::class));

	            return $component;
            }
        );
    }
};