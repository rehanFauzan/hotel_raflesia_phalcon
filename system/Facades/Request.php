<?php
namespace Core\Facades;

/**
 * Class Request
 * @package Core\Facades
 * Encapsulates request information for easy and secure access from application
 * controllers.
 *
 * The request class is a simple value object that is passed between the
 * dispatcher and controller classes. It packages the HTTP request environment.
 *
 * ```php
 *
 * if (Request::isPost() && Request::isAjax()) {
 *     echo "Request was made using POST and AJAX";
 * }
 *
 * // Retrieve SERVER variables
 * Request::getServer("HTTP_HOST");
 *
 * // GET, POST, PUT, DELETE, HEAD, OPTIONS, PATCH, PURGE, TRACE, CONNECT
 * Request::getMethod();
 *
 * // An array of languages the client accepts
 * Request::getLanguages();
 * ```
 * @method static bool getHttpMethodParameterOverride()
 *
 * @method static void setHttpMethodParameterOverride(bool $httpMethodParameterOverride)
 *
 * @method static mixed get(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Gets a variable from the $_REQUEST superglobal applying filters if
 * needed. If no parameters are given the $_REQUEST superglobal is returned
 *
 * ```php
 * // Returns value from $_REQUEST["user_email"] without sanitizing
 * $userEmail = $request->get("user_email");
 *
 * // Returns value from $_REQUEST["user_email"] with sanitizing
 * $userEmail = $request->get("user_email", "email");
 * ```
 *
 * @method static array getAcceptableContent()
 * Gets an array with mime/types and their quality accepted by the
 * browser/client from _SERVER["HTTP_ACCEPT"]
 *
 * @method static ?array getBasicAuth()
 * Gets auth info accepted by the browser/client from
 * $_SERVER["PHP_AUTH_USER"]
 *
 * @method static string getBestAccept()
 * Gets best mime/type accepted by the browser/client from
 * _SERVER["HTTP_ACCEPT"]
 *
 * @method static string getBestCharset()
 * Gets best charset accepted by the browser/client from
 * _SERVER["HTTP_ACCEPT_CHARSET"]
 *
 * @method static string getBestLanguage()
 * Gets best language accepted by the browser/client from
 * _SERVER["HTTP_ACCEPT_LANGUAGE"]
 *
 * @method static string getPreferredIsoLocaleVariant()
 * Gets the preferred ISO locale variant.
 *
 * Gets the preferred locale accepted by the client from the
 * "Accept-Language" request HTTP header and returns the
 * base part of it i.e. `en` instead of `en-US`.
 *
 * Note: This method relies on the `$_SERVER["HTTP_ACCEPT_LANGUAGE"]` header.
 *
 * @link https://www.iso.org/standard/50707.html
 *
 * @method static mixed getClientAddress(bool $trustForwardedHeader = false)
 * Gets most possible client IPv4 Address. This method searches in
 * `$_SERVER["REMOTE_ADDR"]` and optionally in
 * `$_SERVER["HTTP_X_FORWARDED_FOR"]`
 *
 * @method static array getClientCharsets()
 * Gets a charsets array and their quality accepted by the browser/client
 * from _SERVER["HTTP_ACCEPT_CHARSET"]
 *
 * @method static ?string getContentType()
 * Gets content type which request has been made
 *
 * @method static array getDigestAuth()
 * Gets auth info accepted by the browser/client from
 * $_SERVER["PHP_AUTH_DIGEST"]
 *
 * @method static mixed getFilteredQuery(string $name = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Retrieves a query/get value always sanitized with the preset filters
 *
 * @method static mixed getFilteredPost(string $name = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Retrieves a post value always sanitized with the preset filters
 *
 * @method static mixed getFilteredPut(string $name = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Retrieves a put value always sanitized with the preset filters
 *
 * @method static string getHeader(string $header)
 * Gets HTTP header from request data
 *
 * @method static array getHeaders()
 * Returns the available headers in the request
 *
 * <code>
 * $_SERVER = [
 *     "PHP_AUTH_USER" => "phalcon",
 *     "PHP_AUTH_PW"   => "secret",
 * ];
 *
 * $headers = $request->getHeaders();
 *
 * echo $headers["Authorization"]; // Basic cGhhbGNvbjpzZWNyZXQ=
 * </code>
 *
 * @method static string getHttpHost()
 * Gets host name used by the request.
 *
 * `Request::getHttpHost` trying to find host name in following order:
 *
 * - `$_SERVER["HTTP_HOST"]`
 * - `$_SERVER["SERVER_NAME"]`
 * - `$_SERVER["SERVER_ADDR"]`
 *
 * Optionally `Request::getHttpHost` validates and clean host name.
 * The `Request::$strictHostCheck` can be used to validate host name.
 *
 * Note: validation and cleaning have a negative performance impact because
 * they use regular expressions.
 *
 * ```php
 * use Phalcon\Http\Request;
 *
 * $request = new Request;
 *
 * $_SERVER["HTTP_HOST"] = "example.com";
 * $request->getHttpHost(); // example.com
 *
 * $_SERVER["HTTP_HOST"] = "example.com:8080";
 * $request->getHttpHost(); // example.com:8080
 *
 * $request->setStrictHostCheck(true);
 * $_SERVER["HTTP_HOST"] = "ex=am~ple.com";
 * $request->getHttpHost(); // UnexpectedValueException
 *
 * $_SERVER["HTTP_HOST"] = "ExAmPlE.com";
 * $request->getHttpHost(); // example.com
 * ```
 *
 * @method static string getHTTPReferer()
 * Gets web page that refers active request. ie: http://www.google.com
 *
 * @method static mixed getJsonRawBody(bool $associative = false)
 * Gets decoded JSON HTTP raw request body
 *
 * @method static array getLanguages()
 * Gets languages array and their quality accepted by the browser/client
 * from _SERVER["HTTP_ACCEPT_LANGUAGE"]
 *
 * @method static string getMethod()
 * Gets HTTP method which request has been made
 *
 * If the X-HTTP-Method-Override header is set, and if the method is a POST,
 * then it is used to determine the "real" intended HTTP method.
 *
 * The _method request parameter can also be used to determine the HTTP
 * method, but only if setHttpMethodParameterOverride(true) has been called.
 *
 * The method is always an uppercased string.
 *
 * @method static int getPort()
 * Gets information about the port on which the request is made.
 *
 * @method static mixed getPost(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Gets a variable from the $_POST superglobal applying filters if needed
 * If no parameters are given the $_POST superglobal is returned
 *
 * ```php
 * // Returns value from $_POST["user_email"] without sanitizing
 * $userEmail = $request->getPost("user_email");
 *
 * // Returns value from $_POST["user_email"] with sanitizing
 * $userEmail = $request->getPost("user_email", "email");
 * ```
 *
 * @method static mixed getPut(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Gets a variable from put request
 *
 * ```php
 * // Returns value from $_PUT["user_email"] without sanitizing
 * $userEmail = $request->getPut("user_email");
 *
 * // Returns value from $_PUT["user_email"] with sanitizing
 * $userEmail = $request->getPut("user_email", "email");
 * ```
 *
 * @method static mixed getQuery(string $name = null, $filters = null, $defaultValue = null, bool $notAllowEmpty = false, bool $noRecursive = false)
 * Gets variable from $_GET superglobal applying filters if needed
 * If no parameters are given the $_GET superglobal is returned
 *
 * ```php
 * // Returns value from $_GET["id"] without sanitizing
 * $id = $request->getQuery("id");
 *
 * // Returns value from $_GET["id"] with sanitizing
 * $id = $request->getQuery("id", "int");
 *
 * // Returns value from $_GET["id"] with a default value
 * $id = $request->getQuery("id", null, 150);
 * ```
 *
 * @method static string getRawBody()
 * Gets HTTP raw request body
 *
 * @method static string getScheme()
 * Gets HTTP schema (http/https)
 *
 * @method static ?string getServer(string $name)
 * Gets variable from $_SERVER superglobal
 *
 * @method static string getServerAddress()
 * Gets active server address IP
 *
 * @method static string getServerName()
 * Gets active server name
 *
 * @method static array getUploadedFiles(bool $onlySuccessful = false, bool $namedKeys = false)
 * Gets attached files as Phalcon\Http\Request\File instances
 *
 * @method static string getURI(bool $onlyPath = false)
 * Gets HTTP URI which request has been made to
 *
 * ```php
 * // Returns /some/path?with=queryParams
 * $uri = $request->getURI();
 *
 * // Returns /some/path
 * $uri = $request->getURI(true);
 * ```
 *
 * @method static string getUserAgent()
 * Gets HTTP user agent used to made the request
 *
 * @method static bool has(string $name)
 * Checks whether $_REQUEST superglobal has certain index
 *
 * @method static bool hasFiles()
 * Returns if the request has files or not
 *
 * @method static bool hasHeader(string $header)
 * Checks whether headers has certain index
 *
 * @method static bool hasPost(string $name)
 * Checks whether $_POST superglobal has certain index
 *
 * @method static bool hasPut(string $name)
 * Checks whether the PUT data has certain index
 *
 * @method static bool hasQuery(string $name)
 * Checks whether $_GET superglobal has certain index
 *
 * @method static bool hasServer(string $name)
 * Checks whether $_SERVER superglobal has certain index
 *
 * @method static bool isAjax()
 * Checks whether request has been made using ajax
 *
 * @method static bool isConnect()
 * Checks whether HTTP method is CONNECT.
 * if _SERVER["REQUEST_METHOD"]==="CONNECT"
 *
 * @method static bool isDelete()
 * Checks whether HTTP method is DELETE.
 * if _SERVER["REQUEST_METHOD"]==="DELETE"
 *
 * @method static bool isGet()
 * Checks whether HTTP method is GET.
 * if _SERVER["REQUEST_METHOD"]==="GET"
 *
 * @method static bool isHead()
 * Checks whether HTTP method is HEAD.
 * if _SERVER["REQUEST_METHOD"]==="HEAD"
 *
 * @method static bool isMethod($methods, bool $strict = false)
 * Check if HTTP method match any of the passed methods
 * When strict is true it checks if validated methods are real HTTP methods
 *
 * @method static bool isOptions()
 * Checks whether HTTP method is OPTIONS.
 * if _SERVER["REQUEST_METHOD"]==="OPTIONS"
 *
 * @method static bool isPatch()
 * Checks whether HTTP method is PATCH.
 * if _SERVER["REQUEST_METHOD"]==="PATCH"
 *
 * @method static bool isPost()
 * Checks whether HTTP method is POST.
 * if _SERVER["REQUEST_METHOD"]==="POST"
 *
 * @method static bool isPut()
 * Checks whether HTTP method is PUT.
 * if _SERVER["REQUEST_METHOD"]==="PUT"
 *
 * @method static bool isPurge()
 * Checks whether HTTP method is PURGE (Squid and Varnish support).
 * if _SERVER["REQUEST_METHOD"]==="PURGE"
 *
 * @method static bool isSecure()
 * Checks whether request has been made using any secure layer
 *
 * @method static bool isStrictHostCheck()
 * Checks if the `Request::getHttpHost` method will be use strict validation
 * of host name or not
 *
 * @method static bool isSoap()
 * Checks whether request has been made using SOAP
 *
 * @method static bool isTrace()
 * Checks whether HTTP method is TRACE.
 * if _SERVER["REQUEST_METHOD"]==="TRACE"
 *
 * @method static bool isValidHttpMethod(string $method)
 * Checks if a method is a valid HTTP method
 *
 * @method static int numFiles(bool $onlySuccessful = false)
 * Returns the number of files available
 *
 * @method static \Phalcon\Http\RequestInterface setParameterFilters(string $name, array $filters = array(), array $scope = array())
 * Sets automatic sanitizers/filters for a particular field and for
 * particular methods
 *
 * @method static \Phalcon\Http\RequestInterface setStrictHostCheck(bool $flag = true)
 * Sets if the `Request::getHttpHost` method must be use strict validation
 * of host name or not
 *
 */
class Request extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'request';
    }
}