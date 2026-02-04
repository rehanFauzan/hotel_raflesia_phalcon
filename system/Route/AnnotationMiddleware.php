<?php

namespace Core\Route;

use ReflectionClass;
use ReflectionMethod;
use Core\Facades\Annotations;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Registry;

class AnnotationMiddleware
{
    /**
     * @param Registry $registry
     * @param EventsManager $eventsManager
     */
    public function register($registry)
    {
        $middlewares = array_merge($registry->get('middlewares', []), $this->getMiddlewares());
        $registry->set('middlewares', $middlewares);
    }

    public function getMiddlewares()
    {
        $middlewares = [];

        $controllers = $this->getControllerClasses();

        foreach ($controllers as $controller) {
            $middlewares = array_merge($middlewares, $this->getClassMiddlewares($controller));
        }

        return $middlewares;
    }

    public function getClassMiddlewares($class)
    {
        $annotations = Annotations::get($class);

        $classAnnotations = $annotations->getClassAnnotations();
        if (false === $classAnnotations) return [];

        if ($classAnnotations->has('middleware')) {
            $middlewareAnnotation = $classAnnotations->get('middleware');
            $middlewareParams = $middlewareAnnotation->getArguments();
            if (empty($middlewareParams)) return [];

            $middlewareName = array_shift($middlewareParams);
            $middlewareClass = $this->resolveMiddlewareClass($middlewareName);
            if (isset($middlewareClass)) {
                $middlewareGroup = new $middlewareClass($middlewareParams);
            }
        }

        $middlewares = [];

        $reflection = new ReflectionClass($class);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        /** Iterates controller actions */
        foreach ($methods as $method) {
            if ($method->isStatic()) continue;
            if (!str_ends_with($method->name, 'Action')) continue;

            $controllerName = $this->resolveController($reflection->getName());
            $actionName = $this->resolveAction($method->name);
            $middlewareKey = $controllerName . ':' . $actionName;
            if (isset($middlewareGroup)) {
                $this->addMiddleware($middlewares, $middlewareKey, $middlewareGroup);
            }

            $methodAnnotations = Annotations::getMethod($class, $method->name)->getAll('middleware');
            foreach ($methodAnnotations as $annotation) {
                if ($annotation->getName() == 'middleware') {
                    $middlewareParams = $annotation->getArguments();
                    $middlewareName = array_shift($middlewareParams);
                    $middlewareClass = $this->resolveMiddlewareClass($middlewareName);
                    if (isset($middlewareClass)) {
                        $this->addMiddleware($middlewares, $middlewareKey, new $middlewareClass($middlewareParams));
                    }
                }
            }
        }

        return $middlewares;
    }

    private function addMiddleware(&$middelwareStack, $key, $middleware)
    {
        if (!isset($middelwareStack[$key])) {
            $middelwareStack[$key] = [];
        }

        array_push($middelwareStack[$key], $middleware);
    }

    private function getControllerClasses()
    {
        $files = rglob(BASEPATH . '/app/Modules/*/Controller.php');
        $controllers = [];
        foreach ($files as $file) {
            $controller = str_replace(
                [BASEPATH . '/app/', '.php', '/'],
                ['App\\', '', '\\'],
                $file
            );

            if (class_exists($controller)) array_push($controllers, $controller);
        }

        return $controllers;
    }

    private function resolveController($class)
    {
        return substr($class, 16, -10);
    }

    private function resolveAction($method)
    {
        return substr($method, 0, -6);
    }

    private function resolveMiddlewareClass($middlewareName)
    {
        if (class_exists($class = 'App\\Middlewares\\' . $middlewareName)) {
            return $class;
        }
    }
}
