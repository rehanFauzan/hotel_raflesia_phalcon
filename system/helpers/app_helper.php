<?php

declare(strict_types=1);

if(! function_exists('container')) {
    function container($name = null, $parameters = null)
    {
        return $name = null
            ? Core\Kernel::$container
            : Core\Kernel::$container->get($name, $parameters);
    }
}