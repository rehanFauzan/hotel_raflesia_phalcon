<?php

/** @var Phalcon\Mvc\Router $router */

$router->addGet('/{uri:.*}', [
    'controller' => App\Modules\Defaults\Auth\Controller::class,
    'action'     => 'indexAction',
]);