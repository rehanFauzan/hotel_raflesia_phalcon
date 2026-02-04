<?php

declare(strict_types=1);

namespace Core\Route;

abstract class AbstractMiddleware
{
    public function beforeExecute() { }
    public function afterExecute() { }
}