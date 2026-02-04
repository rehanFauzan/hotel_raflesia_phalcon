<?php
namespace Core\Facades;

use RuntimeException;
use InvalidArgumentException;
use Phalcon\Di;

abstract class Facade
{
    // protected static $container;
    protected static $resolvedInstance = [];

    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    public static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        $container = Di::getDefault();
        if ($container && $container->has($name)) {
            return static::$resolvedInstance[$name] = $container[$name];
        }
    }

    protected static function getFacadeAccessor()
    {
        return static::class;
    }

    public static function clearResolvedInstance($name = null)
    {
        if(is_null($name)) {
            $name = static::getFacadeAccessor();
        }

        if(is_string($name)) {
            unset(static::$resolvedInstance[$name]);
        }
    }

    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = [];
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return call_user_func_array([$instance, $method], $args);
    }

    public static function getProperty($prop)
    {
        if(\property_exists(static::getFacadeRoot(), $prop))
        {
            return static::getFacadeRoot()->$prop;
        }

        throw new InvalidArgumentException();
    }

    public static function setProperty($prop, $value)
    {
        if(\property_exists(static::getFacadeRoot(), $prop)) {
            return static::getFacadeRoot()->$prop = $value;
        }

        throw new InvalidArgumentException();
    }
}
