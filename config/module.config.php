<?php

use Repository\Repository;
use Repository\Entity;
use Repository\Hydrator;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'factories' => [
            Repository\RepositoryPluginManager::class => Repository\RepositoryFactory::class,
            Hydrator\PublicProperties::class          => InvokableFactory::class,
        ],
        'shared'    => [
            Hydrator\PublicProperties::class => false
        ]
    ],
    'controllers'     => [
        'factories' => [
            \Repository\Controller\UserController::class => \Repository\Controller\UserControllerFactory::class,
        ]
    ],
    'mappers'         => [
        'maps'    => [
            Entity\User::class => Entity\UserRepository::class,
            Entity\Post::class => Entity\PostRepository::class,
        ],
        'aliases' => [
            'users' => Entity\User::class,
            'posts' => Entity\Post::class,
        ],

        'abstract_factories' => [
            \Repository\Mapper\MapperFactory::class,
        ]
    ],
    'router'          => [
        'routes' => [
            'application' => [
                'type'    => \Zend\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/user[/:action[/:id]]',
                    'defaults' => [
                        'controller' => \Repository\Controller\UserController::class,
                        'action'     => 'index',
                        'id'         => null
                    ],
                ],
            ],
        ],
    ],

];