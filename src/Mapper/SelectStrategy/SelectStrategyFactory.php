<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 21:24
 */

namespace Repository\Mapper\SelectStrategy;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SelectStrategyFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|SelectStrategyPluginManager
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config  = $container->get('config')[SelectStrategyPluginManager::$key] ?? [];
        $plugins = new SelectStrategyPluginManager($container, $config);

        return $plugins;
    }
}