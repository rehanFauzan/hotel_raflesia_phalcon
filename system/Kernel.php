<?php

declare(strict_types=1);

namespace Core;

use Phalcon\Config;
use Phalcon\Mvc;
use Phalcon\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Core\Route\AnnotationRoute;
use Core\Route\AnnotationMiddleware;
use Core\Route\AfterMiddlewareHandler;
use Core\Route\BeforeMiddlewareHandler;

use function Core\Helpers\get_env_aware_files;
use function Core\Helpers\load_env;
use function Core\Helpers\load_config;

final class Kernel
{
    /**
     * @var ?Mvc\Application $application
     */
    public static $application;

    /**
     * @var DiInterface $container
     */
    public static $container;

    /**
     * @var string $requestURI
     */
    public static $requestURI;

    /**
     * @var string $baseURL
     */
    public static $baseURL;

    private function __construct()
    {
    }

    public static function boot()
    {
        assert_options(ASSERT_ACTIVE, 1);
        assert_options(ASSERT_WARNING, 0);
        assert_options(ASSERT_EXCEPTION, 1);

        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (0 === error_reporting())
                return false;

            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        class_exists(Paths::class);

        $kernel = new static();
        $kernel->resolveRequestURI();
        $kernel->loadHelpers();

        $kernel->createContainer();
        $kernel->configure();
        $kernel->registerEventHandler();
        $kernel->registerRoutes();
        $kernel->registerMiddlewares();

        self::$application = new Mvc\Application(self::$container);
        self::$application->setEventsManager(self::$container['eventsManager']);
        return $kernel;
    }

    private function configure()
    {
        $diContainer = self::$container;

        /** @var Config */
        $config = $diContainer->get('config');

        $timezone = $config->get('timezone', 'UTC');
        date_default_timezone_set($timezone);
        Di::setDefault($diContainer);
    }

    /**
     * @return DiInterface
     */
    private function createContainer()
    {
        $diContainer = new Di();

        $dependenciesPath = BASEPATH . '/app/dependencies';
        foreach (glob($dependenciesPath . '/*.php') as $script) {
            $load = function ($script, $diContainer) {
                require $script;
            };

            $load($script, $diContainer);
        }

        assert($diContainer instanceof DiInterface, 'Service provider not set properly');

        $defaultServiceProvider = new DefaultServiceProvider();
        $defaultServiceProvider->register($diContainer);

        self::$container = $diContainer;
    }

    private function loadHelpers()
    {
        $loadCoreHelper = function () {
            require_once 'helpers/autoload.php';
        };

        $loadCoreHelper();

        $load = function ($path) {
            require_once $path;
        };

        foreach (rglob(APP_PATH . '/helpers/*.php') as $path) {
            $load($path);
        }

        load_env();
    }

    private function registerEventHandler()
    {
        if (!file_exists($dir = BASEPATH . '/app/eventHandler') || !is_dir($dir)) {
            return;
        }

        foreach (rglob($dir . '/*.php') as $handlerFile) {
            $load = function ($handlerFile, $event) {
                include $handlerFile;
            };

            $load($handlerFile, self::$container['eventsManager']);
        }

        self::$container['eventsManager']->fire('application:initialized', $this);
    }

    private function registerRoutes()
    {
        $diContainer = self::$container;
        if ($diContainer->has('router')) {
            /** @var Config $config */
            $config = self::$container['config']->merge(['router' => load_config('router')]);

            if ($config->path('router.routes', false)) {
                $router = $diContainer['router'];
                $load = function ($router, $path) {
                    include_once $path;
                };

                foreach ($config->path('router.routes') as $route) {
                    $routeFiles = get_env_aware_files(APP_PATH . '/routes/' . $route . '.php');
                    // routeFiles is an array of candidates (env-aware). Only load when we actually found files
                    if (!empty($routeFiles)) {
                        $routeFile = Arr::last($routeFiles);
                        $load($router, $routeFile);
                    }
                }
            }

            if ($config->path('router.annotationRoute.enabled', false)) {
                (new AnnotationRoute($config))->register();
            }
        }
    }

    private function registerMiddlewares()
    {
        $diContainer = self::$container;

        /** @var Config */
        $config = $diContainer->get('config');

        /** @var Registry */
        $registry = $diContainer->get('registry');

        /** @var EventsManager */
        $eventsManager = $diContainer->get('eventsManager');

        if ($config->has('middlewares')) {
            $globalMiddlewares = [];
            foreach ($config['middlewares'] as $index => $middleware) {
                if (is_string($middleware) && class_exists($middleware)) {
                    $globalMiddlewares[] = new $middleware;
                    continue;
                }

                $diContainer->get('logger')->warning("Couldn't resolve middleware index {$index} -> {$middleware}");
            }
            $registry->set('middlewares', ['#global' => $globalMiddlewares]);
        }

        $eventsManager->attach('dispatch:beforeExecuteRoute', new BeforeMiddlewareHandler);
        $eventsManager->attach('dispatch:afterExecuteRoute', new AfterMiddlewareHandler);

        $annotationMiddleware = new AnnotationMiddleware;
        $annotationMiddleware->register($registry);
    }

    public function run($uri = null)
    {
        $uri = $uri ?? self::$requestURI;

        try {
            $response = self::$application->handle($uri);
        } catch (\Phalcon\Mvc\Dispatcher\Exception $e) {
            $response = self::$application->handle('/panel/Redirect');
        }

        if (!$response->isSent())
            $response->send();
    }

    private function resolveRequestURI()
    {
        if (!defined('STDIN') || PHP_SAPI != 'cli') {
            $requestURI = $_SERVER['REQUEST_URI'];

            $scriptFilename = '/' . trim($_SERVER['SCRIPT_FILENAME'], '/');
            $documentRoot = '/' . trim($_SERVER['DOCUMENT_ROOT'], '/');

            $scriptRelpath = substr($scriptFilename, strlen($documentRoot));


            if (false !== ($queryMarkPos = strpos($requestURI, '?')))
                $requestURI = substr($requestURI, 0, $queryMarkPos);

            while (false !== strpos($requestURI, '//'))
                $requestURI = str_replace('//', '/', $requestURI);

            $index = 0;
            for ($i = 0; $i < min(strlen($scriptRelpath), strlen($requestURI)); $i++) {
                if ($scriptRelpath[$i] == $requestURI[$i]) {
                    if ($scriptRelpath[$i] == '/')
                        $index = $i;
                } else break;
            }

            self::$requestURI = '/' . trim(substr($requestURI, $index), '/');

            $protocol = $this->isHttps() ? 'https://' : 'http://';

            $port = $_SERVER['SERVER_PORT'] ?? '80';
            $portSuffix = ($port != '80' && $port != '443') ? ':' . $port : '';
            self::$baseURL = $protocol . $_SERVER['SERVER_NAME'] . $portSuffix . '/' . trim(substr($requestURI, 0, $index), '/') . '/';
        }
    }

    private function isHttps()
    {
        if (array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        if (array_key_exists("SERVER_PORT", $_SERVER) && 443 === (int) $_SERVER["SERVER_PORT"]) {
            return true;
        }

        if (array_key_exists("HTTP_X_FORWARDED_SSL", $_SERVER) && 'on' === $_SERVER["HTTP_X_FORWARDED_SSL"]) {
            return true;
        }

        if (array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && 'https' === $_SERVER["HTTP_X_FORWARDED_PROTO"]) {
            return true;
        }

        return false;
    }
}
