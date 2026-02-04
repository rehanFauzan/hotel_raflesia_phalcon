<?php

return [
    'appName'           => 'Phoenix',
    'appLongName'       => 'Template Phalcon Phoenix',
    'appDescription'    => '...',
    'appVersion'        => 'v1.0',
    'appKey'            => '@urora@t3knoglobal777',
    'timezone'          => 'Asia/Jakarta',
    'annotationRoute'   => true,
    'enable_cache'      => true,

    'view'              => [
        'autoescape'    => false,
        'directory'     => BASEPATH . '/app/views',
        'always'        => true,
        'path'          => BASEPATH . '/storage/cache/view',
    ],

    'session'           => [
        'savePath'      => BASEPATH . '/storage/session',
        'prefix'        => 'PQSESS_GLOBAL_ACCIS_SRAGEN_AKUNTANSI',
        'uniqid'        => 'PQSESSID_GLOBAL_ACCIS_SRAGEN_AKUNTANSI',
    ],

    'cache' => [
        'lifetime'         => 3 * 3600,
        'viewCache'        => BASEPATH . '/storage/cache/views',
        'dataCache'        => BASEPATH . '/storage/cache/data',
        'annotationsCache' => BASEPATH . '/storage/cache/annotations',
    ],

    'middlewares' => [
        // Controller::class,
    ],
];
