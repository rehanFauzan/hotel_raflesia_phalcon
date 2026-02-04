<?php
namespace Core\Facades;

/**
 * Class Dispatcher
 * @package Core\Facades
 * Dispatching is the process of taking the request object, extracting the
 * module name, controller name, action name, and optional parameters contained
 * in it, and then instantiating a controller and calling an action of that
 * controller.
 *
 * ```php
 * $di = new \Phalcon\Di();
 *
 * $dispatcher = new \Phalcon\Mvc\Dispatcher();
 *
 * $dispatcher->setDI($di);
 *
 * $dispatcher->setControllerName("posts");
 * $dispatcher->setActionName("index");
 * $dispatcher->setParams([]);
 *
 * $controller = $dispatcher->dispatch();
 * ```
 * @method static void forward(array $forward)
 * Forwards the execution flow to another controller/action.
 *
 * ```php
 * use Phalcon\Events\Event;
 * use Phalcon\Mvc\Dispatcher;
 * use App\Backend\Bootstrap as Backend;
 * use App\Frontend\Bootstrap as Frontend;
 *
 * // Registering modules
 * $modules = [
 *     "frontend" => [
 *         "className" => Frontend::class,
 *         "path"      => __DIR__ . "/app/Modules/Frontend/Bootstrap.php",
 *         "metadata"  => [
 *             "controllersNamespace" => "App\Frontend\Controllers",
 *         ],
 *     ],
 *     "backend" => [
 *         "className" => Backend::class,
 *         "path"      => __DIR__ . "/app/Modules/Backend/Bootstrap.php",
 *         "metadata"  => [
 *             "controllersNamespace" => "App\Backend\Controllers",
 *         ],
 *     ],
 * ];
 *
 * $application->registerModules($modules);
 *
 * // Setting beforeForward listener
 * $eventsManager  = $di->getShared("eventsManager");
 *
 * $eventsManager->attach(
 *     "dispatch:beforeForward",
 *     function(Event $event, Dispatcher $dispatcher, array $forward) use ($modules) {
 *         $metadata = $modules[$forward["module"]]["metadata"];
 *
 *         $dispatcher->setModuleName(
 *             $forward["module"]
 *         );
 *
 *         $dispatcher->setNamespaceName(
 *             $metadata["controllersNamespace"]
 *         );
 *     }
 * );
 *
 * // Forward
 * $this->dispatcher->forward(
 *     [
 *         "module"     => "backend",
 *         "controller" => "posts",
 *         "action"     => "index",
 *     ]
 * );
 * ```
 *
 * @method static \Phalcon\Mvc\ControllerInterface getActiveController()
 * Returns the active controller in the dispatcher
 *
 * @method static string getControllerClass()
 * Possible controller class name that will be located to dispatch the
 * request
 *
 * @method static string getControllerName()
 * Gets last dispatched controller name
 *
 * @method static \Phalcon\Mvc\ControllerInterface getLastController()
 * Returns the latest dispatched controller
 *
 * @method static string getPreviousActionName()
 * Gets previous dispatched action name
 *
 * @method static string getPreviousControllerName()
 * Gets previous dispatched controller name
 *
 * @method static string getPreviousNamespaceName()
 * Gets previous dispatched namespace name
 *
 * @method static void setControllerName(string $controllerName)
 * Sets the controller name to be dispatched
 *
 * @method static void setControllerSuffix(string $controllerSuffix)
 * Sets the default controller suffix
 *
 * @method static void setDefaultController(string $controllerName)
 * Sets the default controller name
 * 
 * @method static void callActionMethod($handler, string $actionMethod, array $params = array())
 *
 * @method static bool dispatch()
 * Process the results of the router by calling into the appropriate
 * controller action(s) including any routing data or injected parameters.
 *
 * @method static string getActionName()
 * Gets the latest dispatched action name
 *
 * @method static string getActionSuffix()
 * Gets the default action suffix
 *
 * @method static string getActiveMethod()
 * Returns the current method to be/executed in the dispatcher
 *
 * @method static array getBoundModels()
 * Returns bound models from binder instance
 *
 * ```php
 * class UserController extends Controller
 * {
 *     public function showAction(User $user)
 *     {
 *         // return array with $user
 *         $boundModels = $this->dispatcher->getBoundModels();
 *     }
 * }
 * ```
 *
 * @method static string getDefaultNamespace()
 * Returns the default namespace
 *
 * @method static ManagerInterface getEventsManager()
 * Returns the internal event manager
 *
 * @method static string getHandlerSuffix()
 * Gets the default handler suffix
 *
 * @method static ?BinderInterface getModelBinder()
 * Gets model binder
 *
 * @method static string getModuleName()
 * Gets the module where the controller class is
 *
 * @method static string getNamespaceName()
 * Gets a namespace to be prepended to the current handler name
 *
 * @method static mixed getParam($param, $filters = null, $defaultValue = null)
 * Gets a param by its name or numeric index
 *
 * @method static array getParams()
 * Gets action params
 *
 * @method static bool hasParam($param)
 * Check if a param exists
 *
 * @method static bool isFinished()
 * Checks if the dispatch loop is finished or has more pendent
 * controllers/tasks to dispatch
 *
 * @method static void setActionName(string $actionName)
 * Sets the action name to be dispatched
 *
 * @method static void setDefaultAction(string $actionName)
 * Sets the default action name
 *
 * @method static void setDefaultNamespace(string $namespaceName)
 * Sets the default namespace
 *
 * @method static string getHandlerClass()
 * Possible class name that will be located to dispatch the request
 *
 * @method static void setParam($param, $value)
 * Set a param by its name or numeric index
 *
 * @method static void setParams(array $params)
 * Sets action params to be dispatched
 *
 * @method static void setReturnedValue($value)
 * Sets the latest returned value by an action manually
 *
 * @method static void setActionSuffix(string $actionSuffix)
 * Sets the default action suffix
 *
 * @method static void setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
 * Sets the events manager
 *
 * @method static void setHandlerSuffix(string $handlerSuffix)
 * Sets the default suffix for the handler
 *
 * @method static DispatcherInterface setModelBinder(\Phalcon\Mvc\Model\BinderInterface $modelBinder, $cache = null)
 * Enable model binding during dispatch
 *
 * ```php
 * $di->set(
 *     'dispatcher',
 *     function() {
 *         $dispatcher = new Dispatcher();
 *
 *         $dispatcher->setModelBinder(
 *             new Binder(),
 *             'cache'
 *         );
 *
 *         return $dispatcher;
 *     }
 * );
 * ```
 *
 * @method static void setModuleName(string $moduleName)
 * Sets the module where the controller is (only informative)
 *
 * @method static void setNamespaceName(string $namespaceName)
 * Sets the namespace where the controller class is
 *
 * @method static mixed getReturnedValue()
 * Returns value returned by the latest dispatched action
 *
 * @method static bool wasForwarded()
 * Check if the current executed action was forwarded by another one
 *
 */
class Dispatcher extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dispatcher';
    }
}