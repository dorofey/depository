<?php

use Repository\Mapper\Feature\SelectStrategy;
use Repository\Mapper\Feature\SelectStrategyFeatureFactory;
use Repository\Mapper\SelectStrategy\SelectStrategyFactory;
use Repository\Mapper\SelectStrategy\SelectStrategyPluginManager;
use Repository\Repository;
use Repository\Hydrator;

return [
    'factories' => [
        Repository\RepositoryPluginManager::class => Repository\RepositoryFactory::class,
        SelectStrategyPluginManager::class        => SelectStrategyFactory::class,
        Hydrator\PublicProperties::class          => \Zend\ServiceManager\Factory\InvokableFactory::class,
        SelectStrategy::class                     => SelectStrategyFeatureFactory::class,
    ],
    'shared'    => [
        Hydrator\PublicProperties::class => false,
    ],
];
