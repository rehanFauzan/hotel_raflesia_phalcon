<?php

declare(strict_types=1);

namespace Core\Route;

class AfterMiddlewareHandler extends MiddlewareHandler
{
    public function __invoke()
    {
        $middlewares = $this->getMiddlewares();
        foreach(array_reverse($middlewares) as $middleware) {
            if(is_array($middleware)) {
                $middleware = new $middleware['class'](...$middleware['params']);
            }

            if($middleware instanceof AbstractMiddleware) {
                $result = $middleware->afterExecute();
                if(isset($result)) {
                    return $result;
                }
            }
        }
    }

}