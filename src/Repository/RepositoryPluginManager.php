<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 18/10/2017
 * Time: 09:03
 */

namespace Repository\Repository;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Repository\Mapper\Feature\FeatureAwareInterface;
use Repository\Mapper\Feature\FeatureInterface;
use Repository\Mapper\Feature\FeatureTrait;
use Repository\Mapper\MapperInterface;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\ServiceManager\AbstractPluginManager;

class RepositoryPluginManager extends AbstractPluginManager
{
    protected $maps = [];
    protected $instanceOf = MapperInterface::class;

    /**
     * @param string $name
     * @param array|null $options
     * @return mixed|MapperInterface
     * @throws ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get($name, array $options = null)
    {
        $name = $this->resolveMapperName($name);

        /** @var MapperInterface $instance */
        $instance = parent::get($name, $options);

        $instance->setRepository($this);

        if ($instance instanceof AdapterAwareInterface && $instance::getAdapterClass()) {
            $adapter = $this->creationContext->get($instance::getAdapterClass());
            $instance->setDbAdapter($adapter);
        }

        if ($instance instanceof HydratorAwareInterface && $instance::getHydratorClass()) {
            $hydrator = $this->creationContext->get($instance::getHydratorClass());
            $instance->setHydrator($hydrator);
        }

        if ($instance instanceof FeatureAwareInterface) {
            $this->registerFeatures($this->creationContext, $instance);
        }

        return $instance;
    }

    /**
     * @param ContainerInterface $container
     * @param FeatureAwareInterface $resolvedClass
     * @throws ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function registerFeatures(ContainerInterface $container, $resolvedClass): void
    {
        foreach ($resolvedClass::getFeatures() ?? [] as $featureClass => $options) {
            if (is_int($featureClass)) {
                $featureClass = $options;
                $options      = [];
            }

            if (is_string($featureClass) && $container->has($featureClass)) {
                $featureClass = $container->get($featureClass);
            }

            if (is_string($featureClass) && class_exists($featureClass)) {
                $featureClass = new $featureClass;
            }


            if ($featureClass instanceof FeatureInterface) {
                $resolvedClass->registerFeature($featureClass, $options);
            }
        }
    }

    protected function resolveMapperName($requestedName)
    {
        if (array_key_exists($requestedName, $this->maps)) {
            return $this->maps[$requestedName];
        }

        return $requestedName;
    }

    /**
     * @param array $maps
     * @return $this
     */
    public function setMaps(array $maps = [])
    {
        $this->maps = $maps;

        return $this;
    }

    /**
     * @return array
     */
    public function getMaps(): array
    {
        return $this->maps;
    }
}
