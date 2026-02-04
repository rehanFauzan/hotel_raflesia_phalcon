<?php
namespace Core\Route;

use Core\Facades\Annotations;
use InvalidArgumentException;
use Phalcon\Annotations\Annotation;
use Phalcon\Config;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use ReflectionClass;
use ReflectionMethod;

class AnnotationRoute
{
    private $validMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * @var Dispatcher $dispatcher
     */
    private $dispatcher;

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct($config)
    {
        $this->router = container('router');
        $this->dispatcher = container('dispatcher');
        $this->config = $config;
    }

    public function register()
    {
        $routes = $this->getRoutes();
        $registry = container('registry');

        $resolvedMiddlewares = [];
        $isModular = $this->config->path('router.annotationRoute.isModular', false);
        if($isModular) {
            $defaultModule = $this->config->path('router.annotationRoute.defaultModule', 'Defaults');
            $modules = $this->config->path('router.annotationRoute.modules', []);
            if($modules instanceof Config)
                $modules = $modules->toArray();

            $moduleRoutes = [];
            foreach($routes as $routeKey => $route) {
                [$pattern, $path, $methods, $middlewares] = $route;
                $routeModule = substr($path['controller'], 0, strpos($path['controller'], '\\'));
                if($routeModule == $defaultModule || !array_key_exists($routeModule, $modules))
                    continue;
                
                if(!array_key_exists($pattern, $moduleRoutes))
                    $moduleRoutes[$pattern] = [];
                
                array_push($moduleRoutes[$pattern], $modules[$routeModule]);

                $pattern = $this->cleanPattern('/' . $modules[$routeModule] . $pattern);
                $this->router->add($pattern, $path, $methods);
                unset($routes[$routeKey]);

                $key = $path['controller'] . ':' . $path['action'];
                if(!isset($resolvedMiddlewares[$key]))
                    $resolvedMiddlewares[$key] = $middlewares;
            }
        }

        foreach($routes as $route) {
            [$pattern, $path, $methods, $middlewares] = $route;

            if($isModular) {
                $routeModule = substr($path['controller'], 0, strpos($path['controller'], '\\'));
                $modulePattern = array_key_exists($pattern, $moduleRoutes)
                    ? array_diff(array_values($modules), $moduleRoutes[$pattern])
                    : array_values($modules);
                
                $modulePattern = '{p1:(' . join('|', array_map('preg_quote', $modulePattern)) . ')}';
                $pattern = $this->cleanPattern($modulePattern . $pattern);
            }

            $this->router->add($pattern, $path, $methods);

            $key = $path['controller'] . ':' . $path['action'];
            if(!isset($resolvedMiddlewares[$key]))
                $resolvedMiddlewares[$key] = $middlewares;
        }

        if($isModular) {
            foreach($routes as $route) {
                [$pattern, $path, $methods, $middlewares] = $route;

                $routeModule = substr($path['controller'], 0, strpos($path['controller'], '\\'));
                if($routeModule != $defaultModule)
                    continue;

                $this->router->add($pattern, $path, $methods);

                $key = $path['controller'] . ':' . $path['action'];
                if(!isset($resolvedMiddlewares[$key]))
                    $resolvedMiddlewares[$key] = $middlewares;
            }
        }
        // echo "<pre>\n";var_dump($this->router->getRoutes());exit;
        $registry->set('actionMiddlewares', $resolvedMiddlewares);
    }

    public function getRoutes()
    {
        $routes = [];
        $controllers = $this->getControllerClasses();
        foreach($controllers as $controller) {
            $classRoutes = $this->getClassRoutes($controller);
            $routes = array_merge($routes, $classRoutes);
        }

        return $routes;
    }

    public function getClassRoutes($className)
    {
        $annotations = Annotations::get($className);

        $routeGroup = '';
        $classMiddlewares = [];

        $classAnnotations = $annotations->getClassAnnotations();
        if(false !== $classAnnotations) {
            if($classAnnotations->has('routeGroup')) {
                $routeGroupAnnotation = $classAnnotations->get('routeGroup');
                $routeGroup = isset($routeGroupAnnotation) ? $routeGroupAnnotation->getArgument(0) : '';
                if(!is_string($routeGroup)) {
                    throw new InvalidArgumentException('First parameter of routeGroup must be string.');
                }
            }

            foreach($classAnnotations as $annotation) {
                if($annotation->getName() == 'middleware') {
                    $arguments = $annotation->getArguments();
                    $middlewareClass = MiddlewareHandler::resolveMiddleware(array_shift($arguments));
                    if(false !== $middlewareClass) {
                        array_push($classMiddlewares, [
                            'class'  => $middlewareClass,
                            'params' => $arguments,
                        ]);
                    }
                }
            }
        }

        $routes = [];

        $class = new ReflectionClass($className);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        $actionSuffix = $this->dispatcher->getActionSuffix();

        foreach($methods as $method)
        {
            if($method->isStatic() || !str_ends_with($method->name, $actionSuffix))
                continue;

            /** @var Annotation[] */
            $methodAnnotations = Annotations::getMethod($className, $method->name)->getAnnotations();

            $actionRoutes = [];
            $actionMiddlewares = [];
            foreach($methodAnnotations as $annotation) {
                $isRoute = false;
                if($annotation->getName() == 'route') {
                    $httpMethods = (array) $annotation->getArgument(0);
                    $pattern = $annotation->getArgument(1);
                    $isRoute = true;
                }
                elseif(str_starts_with($annotation->getName(), 'route')) {
                    $httpMethods = [substr($annotation->getName(), 5)];
                    $pattern = $annotation->getArgument(0);
                    $isRoute = true;
                }
                elseif($annotation->getName() == 'middleware') {
                    $arguments = $annotation->getArguments();
                    $middlewareClass = MiddlewareHandler::resolveMiddleware(array_shift($arguments));
                    if(false !== $middlewareClass) {
                        array_push($actionMiddlewares, [
                            'class'  => $middlewareClass,
                            'params' => $arguments,
                        ]);
                    }
                }

                if($isRoute === true) {
                    $httpMethods = array_intersect(
                        array_map('strtoupper', $httpMethods),
                        $this->validMethods,
                    );

                    if(empty($httpMethods))
                        continue;

                    if(!is_string($pattern))
                        throw new InvalidArgumentException('Second parameter of ' . $annotation->getName() . ' must be string.');
                    
                    [$namespace, $controller] = $this->resolveControllerPair($className);

                    array_push($actionRoutes, [
                        $this->cleanPattern($routeGroup . '/' .$pattern), [
                            'namespace'  => $namespace,
                            'controller' => $controller,
                            'action'     => substr($method->name, 0, -strlen($actionSuffix)),
                        ],
                        $httpMethods,
                    ]);
                }
            }

            $actionRoutes = array_map(function($item) use ($classMiddlewares, $actionMiddlewares) {
                array_push($item, array_merge($classMiddlewares, $actionMiddlewares));
                return $item;
            }, $actionRoutes);

            $routes = array_merge($routes, $actionRoutes);
        }

        return $routes;
    }

    private function cleanPattern($pattern)
    {
        while(strpos($pattern, '//'))
            $pattern = str_replace('//', '/', $pattern);
        
        return '/' . trim($pattern, '/');
    }

    /**
     * Resolve controller namespace/class pairs
     */
    private function resolveControllerPair($controllerClass)
    {
        $namespace = $this->config->path('router.annotationRoute.namespace', 'App\\Controller');

        $prefixLength = strlen($namespace);
        $prefixLength = $prefixLength ? ($prefixLength + 1) : 0;
        $suffixLength = strlen($this->dispatcher->getHandlerSuffix());

        $controllerName = !$suffixLength
            ? substr($controllerClass, $prefixLength)
            : substr($controllerClass, $prefixLength, -$suffixLength);

        return [$namespace, $controllerName];
    }

    private function getControllerClasses()
    {
        $basePath = $this->config->path('router.annotationRoute.directory', 'App/Controllers');
        $files = rglob($basePath . '/*' . $this->dispatcher->getHandlerSuffix() . '.php');
        $controllers = [];
        foreach($files as $file) {

            $controller = str_replace(
                [BASEPATH . '/app/', '.php', '/'],
                ['App\\', '', '\\'],
                $file
            );

            if (class_exists($controller) && is_subclass_of($controller, \Phalcon\Mvc\Controller::class)) {
                array_push($controllers, $controller);
            }
        }

        return $controllers;
    }
}