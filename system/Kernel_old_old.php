<?php

declare(strict_types=1);

namespace Core;

use Phalcon\Config;
use Phalcon\Mvc;
use Phalcon\Di;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Core\Route\AnnotationRoute;

use function Core\Helpers\get_env_aware_files;
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
                    if (!empty($routeFile)) {
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

    public function run($uri = null)
    {
        $uri = $uri ?? self::$requestURI;

        $response = self::$application->handle($uri);
        if (!$response->isSent())
            $response->send();
    }

    private function resolveRequestURI()
    {
        if (!defined('STDIN') || PHP_SAPI != 'cli') {
            $requestURI = $_SERVER['REQUEST_URI'];
            $scriptRelpath = substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['DOCUMENT_ROOT']));

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
            self::$baseURL = '/' . trim(substr($requestURI, 0, $index), '/') . '/';
        }
    }
}
