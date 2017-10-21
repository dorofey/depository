<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 21/10/2017
 * Time: 21:51
 */

namespace Repository\Mapper\Feature;


use Interop\Container\ContainerInterface;
use Repository\Mapper\SelectStrategy\SelectStrategyPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class SelectStrategyFeatureFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|SelectStrategy
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $plugins = $container->get(SelectStrategyPluginManager::class);

        return new SelectStrategy($plugins);
    }
}