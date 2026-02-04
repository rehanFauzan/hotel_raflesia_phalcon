<?php
namespace Core\Route;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Registry;

abstract class MiddlewareHandler
{
    /**
     * @return AbstractMiddleware[]|array<array>
     */
    public function getMiddlewares()
    {
        $middlewares = [];

        /** @var Registry $registry */
        $registry = container('registry');

        /** @var Dispatcher $dispatcher */
        $dispatcher = container('dispatcher');

        if($registry->has('globalMiddlewares')) {
            $middlewares = $registry->get('globalMiddlewares');
        }

        if($registry->has('actionMiddlewares')) {
            $actionMiddlewares = $registry->get('actionMiddlewares');
            $action = $dispatcher->getControllerName() . ':' . $dispatcher->getActionName();

            if(array_key_exists($action, $actionMiddlewares))
                $middlewares = array_merge($middlewares, $actionMiddlewares[$action]);
        }

        // var_dump($registry->get('actionMiddlewares'));exit;
        // var_dump($middlewares);exit;

        return $middlewares;
    }

    public static function resolveMiddleware($middlewareName)
    {
        if(!class_exists($resolved = $middlewareName)
            && !class_exists($resolved = 'App\\Middleware\\' . $middlewareName)
            && !class_exists($resolved = 'App\\Middlewares\\' . $middlewareName))
            return false;

        if(!is_subclass_of($resolved, AbstractMiddleware::class))
            return false;

        return $resolved;
    }
}