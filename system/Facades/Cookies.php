<?php
namespace Core\Facades;

/**
 * Class Cookies
 * @package Core\Facades
 *
 * This class is a bag to manage the cookies.
 *
 * A cookies bag is automatically registered as part of the 'response' service
 * in the DI. By default, cookies are automatically encrypted before being sent
 * to the client and are decrypted when retrieved from the user. To set sign key
 * used to generate a message authentication code use
 * `Phalcon\Http\Response\Cookies::setSignKey()`.
 * 
 * @method static bool delete(string $name)
 * Deletes a cookie by its name
 * This method does not removes cookies from the _COOKIE superglobal
 *
 * @method static \Phalcon\Http\Cookie\CookieInterface get(string $name)
 * Gets a cookie from the bag
 *
 * @method static array getCookies()
 * Gets all cookies from the bag
 *
 * @method static bool has(string $name)
 * Check if a cookie is defined in the bag or exists in the _COOKIE
 * superglobal
 *
 * @method static bool isUsingEncryption()
 * Returns if the bag is automatically encrypting/decrypting cookies
 *
 * @method static CookiesInterface reset()
 * Reset set cookies
 *
 * @method static bool send()
 * Sends the cookies to the client
 * Cookies aren't sent if headers are sent in the current request
 *
 * @method static CookiesInterface set(string $name, $value = null, int $expire = 0, string $path = '/', bool $secure = null, string $domain = null, bool $httpOnly = null, array $options = array())
 * Sets a cookie to be sent at the end of the request.
 *
 * This method overrides any cookie set before with the same name.
 *
 * @method static CookiesInterface setSignKey(string $signKey = null)
 * Sets the cookie's sign key.
 *
 * The `$signKey' MUST be at least 32 characters long
 * and generated using a cryptographically secure pseudo random generator.
 *
 * Use NULL to disable cookie signing.
 *
 * @method static CookiesInterface useEncryption(bool $useEncryption)
 * Set if cookies in the bag must be automatically encrypted/decrypted
 */
class Cookies extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cookies';
    }
}