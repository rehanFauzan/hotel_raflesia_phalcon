<?php
namespace App\Middlewares;

use Core\Facades\Session;
use Core\Facades\Dispatcher;
use Core\Route\AbstractMiddleware;

class RequireGuest extends AbstractMiddleware
{
    public function beforeExecute()
    {
        if(Session::has('user')) {
            return Dispatcher::forward([
                'namespace'  => 'App\\Modules',
                'controller' => 'Errors',
                'action'     => 'unauthorized',
                'params'     => ['requireUser'],
            ]);
        }
    }
}