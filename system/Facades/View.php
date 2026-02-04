<?php
namespace Core\Facades;

/**
 * Class View
 * @package Core\Facades
 * Phalcon\Mvc\View is a class for working with the "view" portion of the
 * model-view-controller pattern. That is, it exists to help keep the view
 * script separate from the model and controller scripts. It provides a system
 * of helpers, output filters, and variable escaping.
 * 
 * @method static int getCurrentRenderLevel()
 * @method static bool|array getRegisteredEngines()
 * @method static int getRenderLevel()
 * @method static \Phalcon\Mvc\View cleanTemplateAfter()
 * Resets any template before layouts
 * 
 * @method static \Phalcon\Mvc\View cleanTemplateBefore()
 * Resets any "template before" layouts
 * 
 * @method static \Phalcon\Mvc\ViewInterface disableLevel($level)
 * Disables a specific level of rendering
 * 
 * ```php
 * // Render all levels except ACTION level
 * View::disableLevel(
 *     View::LEVEL_ACTION_VIEW
 * );
 * ```
 * 
 * @method static \Phalcon\Mvc\View disable()
 * Disables the auto-rendering process
 * 
 * @method static \Phalcon\Mvc\View enable()
 * Enables the auto-rendering process
 * 
 * @method static bool exists(string $view)
 * Checks whether view exists
 * 
 * @method static \Phalcon\Mvc\View finish()
 * Finishes the render process by stopping the output buffering
 * 
 * @method static string getActionName()
 * Gets the name of the action rendered
 * 
 * @method static string|array getActiveRenderPath()
 * Returns the path (or paths) of the views that are currently rendered
 * 
 * @method static string getBasePath()
 * Gets base path
 * 
 * @method static string getContent()
 * Returns output from another view stage
 * 
 * @method static string getControllerName()
 * Gets the name of the controller rendered
 * 
 * @method static \Phalcon\Events\ManagerInterface|null getEventsManager()
 * Returns the internal event manager
 * 
 * @method static string getLayout()
 * Returns the name of the main view
 * 
 * @method static string getLayoutsDir()
 * Gets the current layouts sub-directory
 * 
 * @method static string getMainView()
 * Returns the name of the main view
 * 
 * @method static array getParamsToView()
 * Returns parameters to views
 * 
 * @method static string getPartial(string $partialPath, $params = null)
 * Renders a partial view
 * 
 * ```php
 * // Retrieve the contents of a partial
 * echo $this->getPartial("shared/footer");
 * ```
 *
 * ```php
 * // Retrieve the contents of a partial with arguments
 * echo $this->getPartial(
 *     "shared/footer",
 *     [
 *         "content" => $html,
 *     ]
 * );
 * ```
 * 
 * @method static string getPartialsDir()
 * Gets the current partials sub-directory
 * 
 * @method static string getRender(string $controllerName, string $actionName, array $params = array(), $configCallback = null)
 * Perform the automatic rendering returning the output as a string
 *
 * ```php
 * $template = View::getRender(
 *     "products",
 *     "show",
 *     [
 *         "products" => $products,
 *     ]
 * );
 * ```
 * 
 * @method static mixed getVar(string $key)
 * Returns a parameter previously set in the view
 * 
 * @method static string|array getViewsDir()
 * Gets views directory
 * 
 * 
 * @method static bool isDisabled()
 * Whether automatic rendering is enabled
 * 
 * @method static void partial(string $partialPath, $params = null)
 * Renders a partial view
 *
 * ```php
 * // Show a partial inside another view
 * $this->partial("shared/footer");
 * ```
 *
 * ```php
 * // Show a partial inside another view with parameters
 * $this->partial(
 *     "shared/footer",
 *     [
 *         "content" => $html,
 *     ]
 * );
 * ```
 * 
 * @method static \Phalcon\Mvc\View pick($renderView)
 * Choose a different view to render instead of last-controller/last-action
 *
 * ```php
 * use Phalcon\Mvc\Controller;
 *
 * class ProductsController extends Controller
 * {
 *     public function saveAction()
 *     {
 *         // Do some save stuff...
 *
 *         // Then show the list view
 *         View::pick("products/list");
 *     }
 * }
 * ```
 * 
 * @method static \Phalcon\Mvc\View registerEngines(array $engines)
 * Register templating engines
 *
 * ```php
 * View::registerEngines(
 *     [
 *         ".phtml" => \Phalcon\Mvc\View\Engine\Php::class,
 *         ".volt"  => \Phalcon\Mvc\View\Engine\Volt::class,
 *         ".mhtml" => \MyCustomEngine::class,
 *     ]
 * );
 * ```
 * 
 * @method static bool|\Phalcon\Mvc\View render(string $controllerName, string $actionName, array $params = array())
 * Executes render process from dispatching data
 *
 * ```php
 * // Shows recent posts view (app/views/posts/recent.phtml)
 * $view->start()->render("posts", "recent")->finish();
 * ```
 * 
 * @method static \Phalcon\Mvc\View reset()
 * Resets the view component to its factory default values
 * 
 * @method static \Phalcon\Mvc\View setBasePath(string $basePath)
 * Sets base path. Depending of your platform, always add a trailing slash
 * or backslash
 *
 * ```php
 * $view->setBasePath(__DIR__ . "/");
 * ```
 * 
 * @method static \Phalcon\Mvc\View setContent(string $content)
 * Externally sets the view content
 *
 * ```php
 * View::setContent("<h1>hello</h1>");
 * ```
 * 
 * @method static void setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
 * Sets the events manager
 * 
 * @method static \Phalcon\Mvc\View setLayout(string $layout)
 * Change the layout to be used instead of using the name of the latest
 * controller name
 *
 * ```php
 * View::setLayout("main");
 * ```
 * 
 * @method static \Phalcon\Mvc\View setLayoutsDir(string $layoutsDir)
 * Sets the layouts sub-directory. Must be a directory under the views
 * directory. Depending of your platform, always add a trailing slash or
 * backslash
 *
 * ```php
 * $view->setLayoutsDir("../common/layouts/");
 * ```
 * 
 * @method static \Phalcon\Mvc\View setMainView(string $viewPath)
 * Sets default view name. Must be a file without extension in the views
 * directory
 *
 * ```php
 * // Renders as main view views-dir/base.phtml
 * View::setMainView("base");
 * ```
 * 
 * @method static \Phalcon\Mvc\View setPartialsDir(string $partialsDir)
 * Sets a partials sub-directory. Must be a directory under the views
 * directory. Depending of your platform, always add a trailing slash or
 * backslash
 *
 * ```php
 * $view->setPartialsDir("../common/partials/");
 * ```
 * 
 * @method static \Phalcon\Mvc\View setParamToView(string $key, $value)
 * Adds parameters to views (alias of setVar)
 *
 * ```php
 * View::setParamToView("products", $products);
 * ```
 * 
 * @method static \Phalcon\Mvc\ViewInterface setRenderLevel(int $level)
 * Sets the render level for the view
 *
 * ```php
 * // Render the view related to the controller only
 * View::setRenderLevel(
 *     View::LEVEL_LAYOUT
 * );
 * ```
 * 
 * @method static \Phalcon\Mvc\View setTemplateAfter($templateAfter)
 * Sets a "template after" controller layout
 * 
 * @method static \Phalcon\Mvc\View setTemplateBefore($templateBefore)
 * Sets a template before the controller layout
 * 
 * @method static \Phalcon\Mvc\View setVar(string $key, $value)
 * Set a single view parameter
 *
 * ```php
 * View::setVar("products", $products);
 * ```
 * 
 * @method static \Phalcon\Mvc\View setVars(array $params, bool $merge = true)
 * Set all the render params
 *
 * ```php
 * View::setVars(
 *     [
 *         "products" => $products,
 *     ]
 * );
 * ```
 * 
 * @method static \Phalcon\Mvc\View setViewsDir($viewsDir)
 * Sets the views directory. Depending of your platform,
 * always add a trailing slash or backslash
 * 
 * @method static \Phalcon\Mvc\View start()
 * Starts rendering process enabling the output buffering
 * 
 * @method static string toString(string $controllerName, string $actionName, array $params = array())
 * Renders the view and returns it as a string
 * 
 * @method static bool processRender(string $controllerName, string $actionName, array $params = array(), bool $fireEvents = true)
 * Processes the view and templates; Fires events if needed
 */
class View extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'view';
    }
}