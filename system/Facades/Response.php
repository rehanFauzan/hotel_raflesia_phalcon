<?php
namespace Core\Facades;

/**
 * Class Response
 * @package Core\Facades
 * Part of the HTTP cycle is return responses to the clients.
 * Phalcon\HTTP\Response is the Phalcon component responsible to achieve this task.
 * HTTP responses are usually composed by headers and body.
 *
 * ```php
 *
 * Response::setStatusCode(200, "OK");
 * Response::setContent("<html><body>Hello</body></html>");
 *
 * Response::send();
 * ```
 * @method static \Phalcon\Http\ResponseInterface appendContent($content)
 * Appends a string to the HTTP response body
 * 
 * @method static string getContent()
 * Gets the HTTP response body
 * 
 * @method static CookiesInterface getCookies()
 * Returns cookies set by the user
 * 
 * @method static DiInterface getDI()
 * Returns the internal dependency injector
 * 
 * @method static ManagerInterface getEventsManager()
 * Returns the internal event manager
 * 
 * @method static HeadersInterface getHeaders()
 * Returns headers set by the user
 * @method static ?string getReasonPhrase()
 * Returns the reason phrase
 * ```php
 * echo Response::getReasonPhrase();
 * ```
 * @method static ?int getStatusCode()
 * Returns the status code
 * 
 * ```php
 * echo Response::getStatusCode();
 * ```
 * 
 * @method static bool hasHeader(string $name)
 * Checks if a header exists
 * 
 * ```php
 * Response::hasHeader("Content-Type");
 * ```
 * 
 * @method static bool isSent()
 * Check if the response is already sent
 * 
 * @method static \Phalcon\Http\ResponseInterface redirect($location = null, bool $externalRedirect = false, int $statusCode = 302)
 * Redirect by HTTP to another action or URL
 * ```php
 * // Using a string redirect (internal/external)
 * Response::redirect("posts/index");
 * Response::redirect("http://en.wikipedia.org", true);
 * Response::redirect("http://www.example.com/new-location", true, 301);
 * // Making a redirection based on a named route
 * Response::redirect(
 *     [
 *         "for"        => "index-lang",
 *         "lang"       => "jp",
 *         "controller" => "index",
 *     ]
 * );
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface removeHeader(string $name)
 * Remove a header in the response
 * 
 * ```php
 * Response::removeHeader("Expires");
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface resetHeaders()
 * Resets all the established headers
 * 
 * @method static \Phalcon\Http\ResponseInterface send()
 * Prints out HTTP response to the client
 * 
 * @method static \Phalcon\Http\ResponseInterface sendCookies()
 * Sends cookies to the client
 * 
 * @method static void sendHeaders()
 * Sends headers to the client
 * 
 * @method static \Phalcon\Http\ResponseInterface setCache(int $minutes)
 * Sets Cache headers to use HTTP cache
 * 
 * ```php
 * Response::setCache(60);
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setContent(string $content)
 * Sets HTTP response body
 * ```php
 * Response::setContent("<h1>Hello!</h1>");
 * ```
 * @method static \Phalcon\Http\ResponseInterface setContentLength(int $contentLength)
 * Sets the response content-length
 * ```php
 * Response::setContentLength(2048);
 * ```
 * @method static \Phalcon\Http\ResponseInterface setContentType(string $contentType, $charset = null)
 * Sets the response content-type mime, optionally the charset
 * ```php
 * Response::setContentType("application/pdf");
 * Response::setContentType("text/plain", "UTF-8");
 * ```
 * @method static \Phalcon\Http\ResponseInterface setCookies(\Phalcon\Http\Response\CookiesInterface $cookies)
 * Sets a cookies bag for the response externally
 * @method static void setDI(\Phalcon\Di\DiInterface $container)
 * Sets the dependency injector
 * @method static \Phalcon\Http\ResponseInterface setEtag(string $etag)
 * Set a custom ETag
 * ```php
 * Response::setEtag(
 *     md5(
 *         time()
 *     )
 * );
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setExpires(\DateTime $datetime)
 * Sets an Expires header in the response that allows to use the HTTP cache
 * ```php
 * Response::setExpires(
     *     new DateTime()
 * );
 * ```
 * 
 * @method static void setEventsManager(\Phalcon\Events\ManagerInterface $eventsManager)
 * Sets the events manager
 * 
 * @method static \Phalcon\Http\ResponseInterface setFileToSend(string $filePath, $attachmentName = null, $attachment = true)
 * Sets an attached file to be sent at the end of the request
 * 
 * @method static \Phalcon\Http\ResponseInterface setHeader(string $name, $value)
 * Overwrites a header in the response
 * ```php
 * Response::setHeader("Content-Type", "text/plain");
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setHeaders(\Phalcon\Http\Response\HeadersInterface $headers)
 * Sets a headers bag for the response externally
 * 
 * @method static \Phalcon\Http\ResponseInterface setJsonContent($content, int $jsonOptions = 0, int $depth = 512)
 * Sets HTTP response body. The parameter is automatically converted to JSON
 * and also sets default header: Content-Type: "application/json; charset=UTF-8"
 * ```php
 * Response::setJsonContent(
 *     [
 *         "status" => "OK",
 *     ]
 * );
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setLastModified(\DateTime $datetime)
 * Sets Last-Modified header
 * ```php
 * Response::setLastModified(
 *     new DateTime()
 * );
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setNotModified()
 * Sends a Not-Modified response
 * 
 * @method static \Phalcon\Http\ResponseInterface setStatusCode(int $code, string $message = null)
 * Sets the HTTP response code
 * ```php
 * Response::setStatusCode(404, "Not Found");
 * ```
 * 
 * @method static \Phalcon\Http\ResponseInterface setRawHeader(string $header)
 * Send a raw header to the response
 * ```php
 * Response::setRawHeader("HTTP/1.1 404 Not Found");
 * ```
 */
class Response extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'response';
    }
}