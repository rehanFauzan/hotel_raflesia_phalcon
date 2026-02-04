<?php

declare(strict_types=1);

namespace Core\Dispatcher;

class AutoDiscoverHandler
{
    /**
     * @param \Phalcon\Events\Event   $event
     * @param \Phalcon\Mvc\Dispatcher $dispatcher
     */
    public function __invoke($event, $dispatcher)
    {
        $urlStack = array_merge([
                $dispatcher->getControllerName(),
                $dispatcher->getActionName()
            ], $dispatcher->getParams()
        );

        $namespace = $dispatcher->getDefaultNamespace();
        $controllerStack = '';

        foreach ($urlStack as $index => $item) {
            $controller = $controllerStack . ucfirst($item);
            if (class_exists($class = $namespace . '\\' . $controller . 'Controller')) {
                $action = array_key_exists($index + 1, $urlStack) ? $urlStack[$index + 1] : 'index';

                if (method_exists($class, $action . 'Action')) {
                    $dispatcher->forward(
                        [
                            'controller' => $controller,
                            'action'     => $action,
                            'params'     => array_slice($urlStack, $index + 2),
                        ]
                    );

                    return false;
                }

                break;
            }

            $controllerStack .= $controller . '\\';
        }
    }
}