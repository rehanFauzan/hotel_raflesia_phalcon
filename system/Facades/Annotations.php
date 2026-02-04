<?php
namespace Core\Facades;

/**
 * Class Annotations
 * @package Core\Facades
 * Stores the parsed annotations in files. This adapter is suitable for production
 * 
 * @method static \Phalcon\Annotations\Reflection get($className)
 * Parses or retrieves all the annotations found in a class
 *
 * @method static \Phalcon\Annotations\Collection getMethod(string $className, string $methodName)
 * Returns the annotations found in a specific method
 *
 * @method static array getMethods(string $className)
 * Returns the annotations found in all the class' methods
 *
 * @method static \Phalcon\Annotations\Collection getProperty(string $className, string $propertyName)
 * Returns the annotations found in a specific property
 *
 * @method static array getProperties(string $className)
 * Returns the annotations found in all the class' methods
 *
 * @method static \Phalcon\Annotations\ReaderInterface getReader()
 * Returns the annotation reader
 *
 * @method static void setReader(\Phalcon\Annotations\ReaderInterface $reader)
 * Sets the annotations parser
 *
 * @method static bool|int|\Phalcon\Annotations\Reflection read(string $key)
 * Reads parsed annotations from files
 *
 * @method static void write(string $key, \Phalcon\Annotations\Reflection $data)
 * Writes parsed annotations to files
 * 
 */
class Annotations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'annotations';
    }
}