<?php

return [
    'default' => 'main',
    'debug'   => false,
    'connections' => [
        'main' => [
            'adapter' => 'mysql',
            'options' => [
                'host'         => $_ENV['DB_HOST'] ?? getenv('DB_HOST'),
                'port'         => $_ENV['DB_PORT'] ?? getenv('DB_PORT'),
                'dbname'       => $_ENV['DB_NAME'] ?? getenv('DB_NAME'),
                'username'     => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME'),
                'password'     => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD'),
                'dialectClass' => Phalcon\Db\Dialect\Mysql::class,
            ]
        ],
    ],
];