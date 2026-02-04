<?php
namespace Core\Facades;

/**
 * Class Url
 * @package Core\Facades
 * This components helps in the generation of: URIs, URLs and Paths
 *
 * ```php
 * // Generate a URL appending the URI to the base URI
 * echo Url::get("products/edit/1");
 *
 * // Generate a URL for a predefined route
 * echo Url::get(
 *     [
 *         "for"   => "blog-post",
 *         "title" => "some-cool-stuff",
 *         "year"  => "2012",
 *     ]
 * );
 * ```
 * 
 * @method static string get($uri = null, $args = null, bool $local = null, $baseUri = null)
 * Generates a URL
 *
 * ```php
 * // Generate a URL appending the URI to the base URI
 * echo Url::get("products/edit/1");
 *
 * // Generate a URL for a predefined route
 * echo Url::get(
 *     [
 *         "for"   => "blog-post",
 *         "title" => "some-cool-stuff",
 *         "year"  => "2015",
 *     ]
 * );
 *
 * // Generate a URL with GET arguments (/show/products?id=1&name=Carrots)
 * echo Url::get(
 *     "show/products",
 *     [
 *         "id"   => 1,
 *         "name" => "Carrots",
 *     ]
 * );
 *
 * // Generate an absolute URL by setting the third parameter as false.
 * echo Url::get(
 *     "https://phalcon.io/",
 *     null,
 *     false
 * );
 * ```
 *
 * @method static string getBasePath()
 * Returns the base path
 *
 * @method static string getBaseUri()
 * Returns the prefix for all the generated urls. By default /
 *
 * @method static string getStatic($uri = null)
 * Generates a URL for a static resource
 *
 * ```php
 * // Generate a URL for a static resource
 * echo Url::getStatic("img/logo.png");
 *
 * // Generate a URL for a static predefined route
 * echo Url::getStatic(
 *     [
 *         "for" => "logo-cdn",
 *     ]
 * );
 * ```
 *
 * @method static string getStaticBaseUri()
 * Returns the prefix for all the generated static urls. By default /
 *
 * @method static \Phalcon\Url\UrlInterface setBasePath(string $basePath)
 * Sets a base path for all the generated paths
 *
 * ```php
 * Url::setBasePath("/var/www/htdocs/");
 * ```
 *
 * @method static \Phalcon\Url\UrlInterface setBaseUri(string $baseUri)
 * Sets a prefix for all the URIs to be generated
 *
 * ```php
 * Url::setBaseUri("/invo/");
 *
 * Url::setBaseUri("/invo/index.php/");
 * ```
 *
 * @method static \Phalcon\Url\UrlInterface setStaticBaseUri(string $staticBaseUri)
 * Sets a prefix for all static URLs generated
 *
 * ```php
 * Url::setStaticBaseUri("/invo/");
 * ```
 *
 * @method static string path(string $path = null)
 * Generates a local path
 *
 */
class Url extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'url';
    }
}