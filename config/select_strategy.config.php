<?php

use Repository\Mapper\SelectStrategy\Strategies;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
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

];
