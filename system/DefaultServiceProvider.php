<?php

declare(strict_types=1);

namespace Core;

use InvalidArgumentException;
use Phalcon\Url;
use Phalcon\Crypt;
use Phalcon\Debug;
use Phalcon\Config;
use Phalcon\Logger;
use Phalcon\Escaper;
use Phalcon\Mvc\View;
use Phalcon\Registry;
use Phalcon\Security;
use Phalcon\Mvc\Router;
use Phalcon\Http\Request;
use Phalcon\Http\Response;
use Phalcon\Di\DiInterface;
use Core\View\VoltPhpCompiler;
use Phalcon\Filter\FilterFactory;
use Core\Db\Adapter\PdoFactory as PdoSqlSrv;
use Core\Db\Adapter\PdoFactory;
// use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Http\Response\Cookies;
use Core\View\NormalizeViewHandler;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Cache\Adapter\Stream as CacheStream;
use Phalcon\Mvc\View\Engine\Php as PhpViewEngine;
use Phalcon\Logger\Formatter\Json as JsonFormatter;
use Phalcon\Logger\Formatter\Line as LineFormatter;
use Phalcon\Mvc\Model\MetaData\Memory as ModelMetadata;
use Phalcon\Logger\Adapter\Stream as StreamLoggerAdapter;
use Phalcon\Session\Adapter\Stream as StreamSessionAdapter;
use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
use Phalcon\Annotations\Adapter\Stream as AnnotationsStream;
use Phalcon\Helper\Arr;
use Core\Dispatcher\AutoDiscoverHandler;
use Core\Route\AfterMiddlewareHandler;
use Core\Route\AnnotationRoute;
use Core\Route\BeforeMiddlewareHandler;
use Core\Route\MiddlewareHandler;
use Sentry\SentrySdk;
use Sentry\ClientBuilder;
use Sentry\State\Hub;
use Sentry\State\HubAdapter;

use function Core\Helpers\get_env_aware_files;
use function Core\Helpers\load_config;

class DefaultServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers a service provider.
     *
     * @param DiInterface $diContainer
     * @return void
     */
    public function register(DiInterface $diContainer): void
    {
        if (! $diContainer->has('config')) {
            $diContainer->setShared('config', function () {
                return new Config(load_config('config'));
            });
        }

        if (! $diContainer->has('crypt')) {
            $diContainer->setShared('crypt', function () {
                $crypt = new Crypt();

                /** @var Config */
                $config = $this['config']->merge(['crypt' => load_config('crypt')]);

                if ($hashAlgo = $config->path('crypt.hashAlgo')) {
                    $crypt->setHashAlgo($hashAlgo);
                }

                if ($key = $config->path('crypt.key')) {
                    $crypt->setHashAlgo($key);
                }

                if ($cipher = $config->path('crypt.cipher')) {
                    $crypt->setCipher($cipher);
                }

                return $crypt;
            });
        }

        if (! $diContainer->has('request')) {
            $diContainer->setShared('request', function () {
                return new Request();
            });
        }

        if (! $diContainer->has('response')) {
            $diContainer->setShared('response', function () {
                $response = new Response();
                $response->setEventsManager($this['eventsManager']);
                return $response;
            });
        }
        
        if (! $diContainer->has('sentry')) {
            $diContainer->setShared('sentry', function () {
                $clientBuilder = ClientBuilder::create([
                    'dsn' => 'https://acc8669eb2c41a158d9ebd151d0aef12@o4510321294114816.ingest.us.sentry.io/4510321295556608',
                    'environment' => 'production', // bisa 'development'
                    'release' => 'sragen-accis@1.0.0',
                    'send_default_pii' => true,
                ]);

                SentrySdk::setCurrentHub(new Hub($clientBuilder->getClient()));

                return SentrySdk::getCurrentHub();
            });
        }

        if (! $diContainer->has('cookies')) {
            $diContainer->setShared('cookies', function () {
                $cookies = new Cookies();
                $cookies->useEncryption(false);
                return $cookies;
            });
        }

        if (! $diContainer->has('router')) {
            $diContainer->setShared('router', function () {
                $router = new Router();
                $router->setEventsManager($this['eventsManager']);
                return $router;
            });
        }

        if (! $diContainer->has('security')) {
            $diContainer->setShared('security', function () {
                $security = new Security($this['session'], $this['request']);
                return $security;
            });
        }

        if (! $diContainer->has('url')) {
            $diContainer->setShared('url', function () {
                $url = new Url($this['router']);
                $url->setBasePath(BASEPATH);

                /** @var \Phalcon\Config */
                $config = $this['config'];

                $baseURL = $config->get('baseURL', Kernel::$baseURL);
                $url->setBaseUri($baseURL);

                return $url;
            });
        }

        if (! $diContainer->has('filter')) {
            $diContainer->setShared('filter', function () {
                $filterFactory = new FilterFactory();
                return $filterFactory->newInstance();
            });
        }

        if (! $diContainer->has('logger')) {
            $diContainer->setShared('logger', function () {
                $logger = new Logger('main');

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['logger' => load_config('logger')]);

                $logPath = $config->path('logger.path', BASEPATH . '/storage/logs');
                $logPath = rtrim($logPath, "\\/") . '/';
                $logPath .= $config->path('logger.prefix');
                $logPath .= date('Y-m-d');
                if ($ext = $config->path('logger.extension')) {
                    $logPath .= '.' . ltrim($ext, '.');
                }

                if (!file_exists($dir = dirname($logPath))) {
                    @mkdir($dir, 0775, true);
                }

                $dateFormat = $config->path('logger.dateFormat', 'c');
                $logFormat = $config->path('logger.logFormat', '[%date%][%type%] %message%');

                $lineLoggerAdapter = new StreamLoggerAdapter($logPath, ['mode' => 'a+']);
                $lineLoggerAdapter->setFormatter(new LineFormatter($logFormat, $dateFormat));

                $logger->addAdapter('file', $lineLoggerAdapter);

                $logger->setLogLevel($config->path('logger.logLevel', Logger::WARNING));

                return $logger;
            });
        }

        if (! $diContainer->has('eventsManager')) {
            $diContainer->setShared('eventsManager', function () {
                return new EventsManager();
            });
        }

        if (! $diContainer->has('dispatcher')) {
            $diContainer->setShared('dispatcher', function () {
                $dispatcher = new MvcDispatcher();

                /** @var EventsManager $eventsManager */
                $eventsManager = $this['eventsManager'];
                $dispatcher->setEventsManager($eventsManager);

                /** @var Config $config */
                $config = $this['config']->merge(['dispatcher' => load_config('dispatcher')]);

                $actionSuffix = $config->path('dispatcher.actionSuffix', 'Action');
                $dispatcher->setActionSuffix($actionSuffix);

                $controllerSuffix = $config->path('dispatcher.controllerSuffix', 'Controller');
                $dispatcher->setControllerSuffix($controllerSuffix);

                $handlerSuffix = $config->path('dispatcher.handlerSuffix', 'Controller');
                $dispatcher->setHandlerSuffix($handlerSuffix);

                $defaultNamespace = $config->path('dispatcher.defaultNamespace', 'App\\Controllers');
                $dispatcher->setDefaultNamespace($defaultNamespace);

                $defaultController = $config->path('dispatcher.defaultController', 'Index');
                $dispatcher->setDefaultController($defaultController);

                $defaultAction = $config->path('dispatcher.defaultAction', 'index');
                $dispatcher->setDefaultAction($defaultAction);

                if ($config->path('dispatcher.autoDiscover', false)) {
                    $this['eventsManager']->attach('dispatch:beforeDispatchLoop', new AutoDiscoverHandler);
                }

                $eventsManager->attach('dispatch:beforeExecuteRoute', new BeforeMiddlewareHandler);
                $eventsManager->attach('dispatch:afterExecuteRoute', new AfterMiddlewareHandler);

                /** @var Registry $registry */
                $registry = $this['registry'];

                $middlewares = $config->path('middlewares');
                if ($middlewares instanceof Config) {
                    $middlewares = $middlewares->toArray();
                } elseif (!is_array($middlewares)) {
                    $middlewares = [$middlewares];
                }

                $globalMiddlewares = [];
                foreach ($middlewares as $middlewareName) {
                    $middlewareClass = MiddlewareHandler::resolveMiddleware($middlewareName);
                    if (false !== $middlewareClass)
                        array_push($globalMiddlewares, $middlewareClass);
                }


                $registry->set('globalMiddlewares', $globalMiddlewares);

                return $dispatcher;
            });
        }

        if (! $diContainer->has('session')) {
            $diContainer->setShared('session', function () {
                $session = new SessionManager();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['session' => load_config('session')]);

                $savePath = $config->path('session.savePath', BASEPATH . '/storage/session');
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0775, true);
                }

                $streamAdapter = new StreamSessionAdapter([
                    'prefix'   => $config->path('session.prefix', 'PQSESS_'),
                    'savePath' => $savePath,
                ]);

                $session->setAdapter($streamAdapter);

                $session->start();
                return $session;
            });
        }

        if (! $diContainer->has('db')) {
            $diContainer->setShared('db', function () {
                $dbFactory = new PdoFactory();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                $key = $config->path('database.default', 'main');
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }


        if (!$diContainer->has('db_simpel_sragen')) {
            $diContainer->setShared('db_simpel_sragen', function () {
                $dbFactory = new PdoFactory();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'simpel_sragen';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");
                
                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_situntas_asasta')) {
            $diContainer->setShared('db_situntas_asasta', function () {
                $dbFactory = new PdoFactory();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'situntas_asasta';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_sirantas_asasta')) {
            $diContainer->setShared('db_sirantas_asasta', function () {
                $dbFactory = new PdoFactory();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'sirantas_asasta';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_tirta_benteng')) {
            $diContainer->setShared('db_tirta_benteng', function () {
                $dbFactory = new PdoFactory();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'billing_tirtabenteng';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_bacameter_bogor')) {
            $diContainer->setShared('db_bacameter_bogor', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'bacameter_bogor';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_billing_bogor')) {
            $diContainer->setShared('db_billing_bogor', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'billing_bogor';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_simentor_tarum')) {
            $diContainer->setShared('db_simentor_tarum', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'simentor_accis_tarum';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_billing_tarum')) {
            $diContainer->setShared('db_billing_tarum', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'billing_tirtatarum_karawang';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_kepeg_tarum')) {
            $diContainer->setShared('db_kepeg_tarum', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'kepeg_tirtatarum_karawang';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_tjm')) {
            $diContainer->setShared('db_tjm', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'tjm';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_situntas_global')) {
            $diContainer->setShared('db_situntas_global', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'situntas_global';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_pengaduan')) {
            $diContainer->setShared('db_pengaduan', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'pengaduan';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_d2d')) {
            $diContainer->setShared('db_d2d', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'd2d';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (!$diContainer->has('db_tbw')) {
            $diContainer->setShared('db_tbw', function () {
                $dbFactory = new PdoSqlSrv();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['database' => load_config('database')]);

                // $key = $config->path('database.default', 'situntas_asasta');
                $key = 'tbw';
                if (false === ($dbConfig = $config->path('database.connections.' . $key, false)))
                    throw new InvalidArgumentException("No config for db.{$key} is found.");

                return $dbFactory->load($dbConfig);
            });
        }

        if (! $diContainer->has('debug')) {
            $diContainer->setShared('debug', function () {
                return new Debug;
            });
        }

        if (! $diContainer->has('viewCache')) {
            $diContainer->setShared('viewCache', function () {
                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['cache' => load_config('cache')]);

                $cacheDir = $config->path('cache.viewCache', BASEPATH . '/storage/cache/views');
                $cacheDir = rtrim($cacheDir, '/\\') . '/';
                if (!file_exists($cacheDir)) {
                    mkdir($cacheDir, 0775, true);
                }

                $lifetime = $config->path('cache.lifetime', 3 * 3600);
                return new CacheStream(new SerializerFactory(), [
                    'storageDir' => $cacheDir,
                    'lifetime'   => $lifetime,
                ]);
            });
        }

        if (! $diContainer->has('dataCache')) {
            $diContainer->setShared('dataCache', function () {
                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['cache' => load_config('cache')]);

                $cacheDir = $config->path('cache.dataCache', BASEPATH . '/storage/cache/data');
                $cacheDir = rtrim($cacheDir, '/\\') . '/';
                if (!file_exists($cacheDir)) {
                    mkdir($cacheDir, 0775, true);
                }

                $lifetime = $config->path('cache.lifetime', 3 * 3600);
                return new CacheStream(new SerializerFactory(), [
                    'storageDir' => $cacheDir,
                    'lifetime'   => $lifetime,
                ]);
            });
        }

        if (! $diContainer->has('modelsManager')) {
            $diContainer->setShared('modelsManager', function () {
                $modelsManager = new ModelsManager();
                $modelsManager->setEventsManager($this['eventsManager']);
                return $modelsManager;
            });
        }

        if (! $diContainer->has('escaper')) {
            $diContainer->setShared('escaper', function () {
                return new Escaper;
            });
        }

        if (! $diContainer->has('flash')) {
            $diContainer->setShared('flash', function () {
                $flashSession = new FlashSession();
                $flashSession->setEscaperService($this['escaper']);
                $flashSession->setCssClasses([
                    'error'   => 'text-bg-danger',
                    'success' => 'text-bg-success',
                    'warning' => 'text-bg-warning',
                    'info'    => 'text-bg-info',
                ]);

                $flashSession->setCustomTemplate('
                    <div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
                        <div class="toast bg-warning fade show" id="liveToast" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header">
                                <strong class="me-auto text-white">Informasi</strong>
                                <button class="btn ms-2 p-0" type="button" data-bs-dismiss="toast" aria-label="Close"><span class="uil uil-times fs-7"></span></button>
                            </div>
                            <div class="toast-body text-white">%message%</div>
                        </div>
                    </div>
                ');
                return $flashSession;
            });
        }

        if (! $diContainer->has('annotations')) {
            $diContainer->setShared('annotations', function () {
                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['cache' => load_config('cache')]);

                if (defined('ENVIRONMENT') && strtolower(ENVIRONMENT) == 'development') {
                    return new AnnotationsMemory();
                }

                $cacheDir = $config->path('cache.annotationsCache', BASEPATH . '/storage/cache/annotations');
                if (!file_exists($cacheDir)) {
                    mkdir($cacheDir, 0775, true);
                }

                $cacheDir = rtrim($cacheDir, '/\\') . '/';

                return new AnnotationsStream(['annotationsDir' => $cacheDir]);
            });
        }

        if (! $diContainer->has('registry')) {
            $diContainer->setShared('registry', function () {
                return new Registry();
            });
        }

        if (! $diContainer->has('modelsMetadata')) {
            $diContainer->setShared('modelsMetadata', function () {
                return new ModelMetadata();
            });
        }

        if (!$diContainer->has('view')) {
            $diContainer->setShared('view', function () use ($diContainer) {
                $view = new View();

                /** @var \Phalcon\Config */
                $config = $this['config']->merge(['view' => load_config('view')]);

                $viewsDir = $config->path('view.directory', BASEPATH . '/app/views');
                $path = $config->path('view.path', BASEPATH . '/storage/cache/volt');

                if (!file_exists($path)) {
                    mkdir($path, 0775, true);
                }

                $options = $config->get('view');
                $options['path'] = function ($templatePath) use ($path, $viewsDir) {
                    $fileName = substr($templatePath, strlen($viewsDir) + 2);
                    $fileNameArray = explode('.', $fileName, -1);
                    $fileName = join('_', $fileNameArray);
                    $fileName = strtolower($fileName);
                    $fileName = str_replace(['/', '\\'], ['__', '__'], $fileName);
                    $fileName = $fileName . '.php';
                    return $path . '/' . $fileName;
                };

                $view->setViewsDir($viewsDir . '/');
                $view->registerEngines([
                    '.volt' => function ($view) use ($options, $diContainer) {
                        $volt  = new VoltEngine($view, $diContainer);
                        $volt->setOptions($options->toArray());
                        $volt->setEventsManager($diContainer['eventsManager']);

                        /**
                         * Register the PHP extension, to be able to use PHP
                         * functions in Volt
                         */
                        $volt->getCompiler()->addExtension(new VoltPhpCompiler());

                        return $volt;
                    },
                    '.phtml' => PhpViewEngine::class,
                    '.php' => PhpViewEngine::class,
                ]);

                $view->setEventsManager($diContainer['eventsManager']);
                $diContainer['eventsManager']->attach('application:viewRender', new NormalizeViewHandler);

                return $view;
            });
        }

        $this->registerDatabaseServices($diContainer);
    }

    private function registerDatabaseServices(DiInterface $diContainer)
    {
        $config = $diContainer['config']->merge(['database' => load_config('database')]);
        $databaseConnections = $config->path('database.connections', false);
        if ($databaseConnections instanceof Config) {
            $databaseConnections = $databaseConnections->toArray();
            foreach ($databaseConnections as $key => $dbConfig) {
                $diContainer->setShared('db.' . $key, function () use ($dbConfig) {
                    $dbFactory = new PdoFactory();

                    return $dbFactory->load($dbConfig);
                });
            }
        }
    }
}
