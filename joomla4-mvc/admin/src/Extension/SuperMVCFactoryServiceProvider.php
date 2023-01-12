<?php

namespace Robbie\Component\Helloworld\Administrator\Extension;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\ApiMVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\SiteRouter;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Robbie\Component\Helloworld\Administrator\Extension\SuperMVCFactory;

class SuperMVCFactoryServiceProvider implements ServiceProviderInterface
{
    /**
     * The extension namespace
     *
     * @var  string
     *
     * @since   4.0.0
     */
    private $namespace;

    /**
     * MVCFactory constructor.
     *
     * @param   string  $namespace  The namespace
     *
     * @since   4.0.0
     */
    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                /*
                if (\Joomla\CMS\Factory::getApplication()->isClient('api')) {
                    $factory = new ApiMVCFactory($this->namespace);
                } else {
                    $factory = new \Joomla\CMS\MVC\Factory\MVCFactory($this->namespace);
                }
                */
                // create a SuperMVCFactory instance instead of a MVCFactory instance
                $factory = new SuperMVCFactory($this->namespace);

                $factory->setFormFactory($container->get(FormFactoryInterface::class));
                $factory->setDispatcher($container->get(DispatcherInterface::class));
                $factory->setDatabase($container->get(DatabaseInterface::class));
                $factory->setSiteRouter($container->get(SiteRouter::class));
                $factory->setCacheControllerFactory($container->get(CacheControllerFactoryInterface::class));
                
                // make our SuperMVCFactory instance aware of the CategoryFactory instance
                $factory->setCategoryFactory($container->get(CategoryFactoryInterface::class));

                return $factory;
            }
        );
    }
}