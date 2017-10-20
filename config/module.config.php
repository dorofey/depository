<?php

use Repository\Repository;
use Repository\Hydrator;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            Repository\RepositoryPluginManager::class => Repository\RepositoryFactory::class,
            Hydrator\PublicProperties::class          => InvokableFactory::class,
        ],
        'shared'    => [
            Hydrator\PublicProperties::class => false,
        ],
    ],
];