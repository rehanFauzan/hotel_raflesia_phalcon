<?php

declare(strict_types=1);

namespace Core\Route;

class BeforeMiddlewareHandler extends MiddlewareHandler
{
    public function __invoke()
    {
        $middlewares = $this->getMiddlewares();
        foreach($middlewares as $middleware) {
            if(is_array($middleware)) {
                $middleware = new $middleware['class'](...$middleware['params']);
            }

            if($middleware instanceof AbstractMiddleware) {
                $result = $middleware->beforeExecute();
                if(isset($result)) {
                    return $result;
                }
            }
        }
    }
}