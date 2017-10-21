<?php

use Repository\Mapper\Feature\SelectStrategy;
use Repository\Mapper\Feature\SelectStrategyFeatureFactory;
use Repository\Mapper\SelectStrategy\SelectStrategyFactory;
use Repository\Mapper\SelectStrategy\SelectStrategyPluginManager;
use Repository\Repository;
use Repository\Hydrator;
use Repository\Mapper\SelectStrategy\Strategies;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            Repository\RepositoryPluginManager::class => Repository\RepositoryFactory::class,
            SelectStrategyPluginManager::class        => SelectStrategyFactory::class,
            Hydrator\PublicProperties::class          => InvokableFactory::class,
            SelectStrategy::class                     => SelectStrategyFeatureFactory::class
        ],
        'shared'    => [
            Hydrator\PublicProperties::class => false,
        ],
    ],
    'select_strategy' => [
        'factories' => [
            Strategies\Order::class       => InvokableFactory::class,
            Strategies\Random::class      => InvokableFactory::class,
            Strategies\AndStrategy::class => InvokableFactory::class,
            Strategies\OrStrategy::class  => InvokableFactory::class,
            Strategies\Between::class     => InvokableFactory::class,
            Strategies\Limit::class       => InvokableFactory::class,
            Strategies\Offset::class      => InvokableFactory::class,
        ],
        'aliases'   => [
            'order'   => Strategies\Order::class,
            'random'  => Strategies\Random::class,
            'where'   => Strategies\AndStrategy::class,
            'or'      => Strategies\OrStrategy::class,
            'between' => Strategies\Between::class,
            'limit'   => Strategies\Limit::class,
            'offset'  => Strategies\Offset::class,
        ]
    ]
];