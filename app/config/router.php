<?php
return [
    'annotationRoute' => [
        'enabled'       => true,
        'namespace'     => 'App\\Modules',
        'directory'     => APP_PATH . '/Modules',
        'isModular'     => true,
        'defaultModule' => 'Defaults',
        'modules'       => [
            'Defaults'  => 'panel',
            'aurora'   => 'aurora'
        ],
    ],
    'routes' => ['main'],
];
