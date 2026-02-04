<?php
namespace Core\View;

/**
 * Normalisasi path dari class ke url
 */
class NormalizeViewHandler
{
    public function __invoke($event, $application, $view)
    {
        $dispatcher = $application->dispatcher;
        $controllerName = str_replace('\\', '/', $dispatcher->getControllerName());
        $view->render($controllerName, $dispatcher->getActionName());
        return false;
    }
}