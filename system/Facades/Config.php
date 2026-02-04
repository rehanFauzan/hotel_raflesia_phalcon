<?php
namespace Core\Facades;

/**
 * Class Config
 * @package Core\Facades
 * `Phalcon\Config` is designed to simplify the access to, and the use of,
 * configuration data within applications. It provides a nested object property
 * based user interface for accessing this configuration data within application
 * code.
 * 
 * @method static string getPathDelimiter()
 * Gets the default path delimiter
 *
 * @method static \Phalcon\Config\ConfigInterface merge($toMerge)
 * Merges a configuration into the current one
 *
 * ```php
 * $appConfig = new \Phalcon\Config(
 * [
 *     "database" => [
 *     "host" => "localhost",
 *     ],
 * ]
 * );
 *
 * $globalConfig->merge($appConfig);
 * ```
 *
 * @method static mixed path(string $path, $defaultValue = null, $delimiter = null)
 * Returns a value from current config using a dot separated path.
 *
 * ```php
 * echo $config->path("unknown.path", "default", ".");
 * ```
 *
 * @method static ConfigInterface setPathDelimiter(string $delimiter = null)
 * Sets the default path delimiter
 *
 * @method static array toArray()
 * Converts recursively the object to an array
 *
 * ```php
 * print_r(
 * $config->toArray()
 * );
 * ```
 *
 * @method static void clear()
 * Clears the internal collection
 *
 * @method static int count()
 * Count elements of an object.
 * See [count](https://php.net/manual/en/countable.count.php)
 *
 * @method static mixed|null get(string $element, $defaultValue = null, string $cast = null)
 * Get the element from the collection
 *
 * @method static \Traversable getIterator()
 * Returns the iterator of the class
 *
 * @method static array getKeys(bool $insensitive = true)
 *
 * @method static array getValues()
 *
 * @method static bool has(string $element)
 * Determines whether an element is present in the collection.
 *
 * @method static void init(array $data = array())
 * Initialize internal array
 *
 * @method static array jsonSerialize()
 * Specify data which should be serialized to JSON
 * See [jsonSerialize](https://php.net/manual/en/jsonserializable.jsonserialize.php)
 *
 * @method static bool offsetExists($element)
 * Whether a offset exists
 * See [offsetExists](https://php.net/manual/en/arrayaccess.offsetexists.php)
 *
 * @method static mixed offsetGet($element)
 * Offset to retrieve
 * See [offsetGet](https://php.net/manual/en/arrayaccess.offsetget.php)
 *
 * @method static void offsetSet($element, $value)
 * Offset to set
 * See [offsetSet](https://php.net/manual/en/arrayaccess.offsetset.php)
 *
 * @method static void offsetUnset($element)
 * Offset to unset
 * See [offsetUnset](https://php.net/manual/en/arrayaccess.offsetunset.php)
 *
 * @method static void remove(string $element)
 * Delete the element from the collection
 *
 * @method static void set(string $element, $value)
 * Set an element in the collection
 *
 * @method static string serialize()
 * String representation of object
 * See [serialize](https://php.net/manual/en/serializable.serialize.php)
 *
 * @method static array toArray()
 *
 * @method static string toJson(int $options = 79)
 * Returns the object in a JSON format
 *
 * The default string uses the following options for json_encode
 *
 * `JSON_HEX_TAG`, `JSON_HEX_APOS`, `JSON_HEX_AMP`, `JSON_HEX_QUOT`,
 * `JSON_UNESCAPED_SLASHES`
 *
 * See [rfc4627](https://www.ietf.org/rfc/rfc4627.txt)
 *
 * @method static mixed unserialize($serialized)
 * Constructs the object
 * See [unserialize](https://php.net/manual/en/serializable.unserialize.php)
 */
class Config extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}