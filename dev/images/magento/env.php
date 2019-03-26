<?php

return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => $_ENV['MYSQL_HOST'],
                'dbname' => $_ENV['MYSQL_DATABASE'],
                'username' => $_ENV['MYSQL_USER'],
                'password' =>  $_ENV['MYSQL_PASSWORD'],
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;',
                'active' => '1'
            ]
        ]
    ],
    'session' => [
        'save' => 'redis',
        'redis' => [
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => $_ENV['REDIS_HOST'],
                    'port' => $_ENV['REDIS_PORT'],
                    'database' => $_ENV['REDIS_CACHE_DB'],
                ]
            ],
            'page_cache' => [
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => $_ENV['REDIS_HOST'],
                    'port' => $_ENV['REDIS_PORT'],
                    'database' => $_ENV['REDIS_FPC_DB'],
                ]
            ]
        ]
    ]
];
