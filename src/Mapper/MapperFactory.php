<?php

namespace Repository\Mapper;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Repository\Mapper\Feature\FeatureInterface;
use Repository\Mapper\SelectStrategy\SelectStrategyAwareInterface;
use Repository\Repository\RepositoryPluginManager;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Hydrator\HydratorAwareInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class MapperFactory implements AbstractFactoryInterface
{
    protected $maps = [];
    protected $mapsInitialized = false;
    /** @var  RepositoryPluginManager */
    protected $pluginManager;

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return StandardMapper
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $resolvedName = $this->resolveMapperName($container, $requestedName);

        /** @var StandardMapper $resolvedClass */
        $resolvedClass = $this->getPluginManager()->get($resolvedName);
        $resolvedClass->setRepository($this->getPluginManager());

        if ($resolvedClass instanceof AdapterAwareInterface && $resolvedClass::getAdapterClass()) {
            $adapter = $container->get($resolvedClass::getAdapterClass());
            $resolvedClass->setDbAdapter($adapter);
        }

        if ($resolvedClass instanceof HydratorAwareInterface && $resolvedClass::getHydratorClass()) {
            $hydrator = $container->get($resolvedClass::getHydratorClass());
            $resolvedClass->setHydrator($hydrator);
        }

        $this->registerFeatures($container, $resolvedClass);

        return $resolvedClass;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function resolveMapperName(ContainerInterface $container, $requestedName)
    {
        if ($this->mapsInitialized === false) {
            $this->maps            = $this->getPluginManager($container)->getMaps();
            $this->mapsInitialized = true;
        }

        if (array_key_exists($requestedName, $this->maps)) {
            return $this->maps[$requestedName];
        }

        return false;
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->resolveMapperName($container, $requestedName) !== false;
    }

    /**
     * @param null|ContainerInterface $container
     * @return RepositoryPluginManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPluginManager($container = null): RepositoryPluginManager
    {
        if (null === $this->pluginManager && null !== $container) {
            $this->pluginManager = $container->get(RepositoryPluginManager::class);
        }

        return $this->pluginManager;
    }

    /**
     * @param ContainerInterface $container
     * @param StandardMapper $resolvedClass
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function registerFeatures(ContainerInterface $container, $resolvedClass): void
    {
        foreach ($resolvedClass::getFeatures() as $featureClass => $options) {
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
}