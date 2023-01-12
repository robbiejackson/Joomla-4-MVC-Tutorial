<?php

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
//use Joomla\CMS\Extension\Service\Provider\MVCFactory; - replaced by line below
use Robbie\Component\Helloworld\Administrator\Extension\SuperMVCFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\HTML\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Robbie\Component\Helloworld\Administrator\Extension\HelloworldComponent;
use Robbie\Component\Helloworld\Administrator\Helper\AssociationsHelper;
use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\Database\DatabaseInterface;


return new class implements ServiceProviderInterface {
    
    public function register(Container $container): void 
    {
        $container->set(AssociationExtensionInterface::class, new AssociationsHelper());
        
        $container->registerServiceProvider(new CategoryFactory('\\Robbie\\Component\\Helloworld'));
        // use the SuperMVCFactory Service Provider instead of the MVCFactory one
        $container->registerServiceProvider(new SuperMVCFactoryServiceProvider('\\Robbie\\Component\\Helloworld'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Robbie\\Component\\Helloworld'));
        $container->registerServiceProvider(new RouterFactory('\\Robbie\\Component\\Helloworld'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new HelloworldComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));
                $component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setDatabase($container->get(DatabaseInterface::class));

                return $component;
            }
        );
    }
};