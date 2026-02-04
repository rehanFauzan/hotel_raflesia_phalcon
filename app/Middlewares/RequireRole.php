<?php
namespace App\Middlewares;

use Core\Facades\Session;
use Core\Facades\Dispatcher;
use Core\Route\AbstractMiddleware;

class RequireRole extends AbstractMiddleware
{
    private $roles = [];

    function __construct($roles)
    {
        $this->roles = (array) $roles;
    }

    public function beforeExecute()
    {
        if(!Session::has('user') || !in_array(Session::get('user')['hak_akses'], $this->roles)) {
            return Dispatcher::forward([
                'namespace'  => 'App\\Modules',
                'controller' => 'Defaults\\Errors',
                'action'     => 'unauthorized',
                'params'     => ['requireRole', $this->roles],
            ]);
        }
    }
}