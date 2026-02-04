<?php
namespace Core\Facades;

/**
 * Class Flash
 * @package Core\Facades
 * Shows HTML notifications related to different circumstances. Classes can be
 * stylized using CSS
 *
 * ```php
 * Flash::success("The record was successfully deleted");
 * Flash::error("Cannot open the file");
 * ```
 * 
 * @method static array getMessages($type = null, bool $remove = true)
 * Returns the messages in the session flasher
 * 
 * @method static string|null getMessage(string $type, string $message)
 * Adds a message to the session flasher
 * 
 * @method static bool has($type = null)
 * Checks whether there are messages
 * 
 * @method static bool getAutoescape()
 *
 * @method static array getCssClasses()
 *
 * @method static string getCustomTemplate()
 *
 * @method static void clear()
 * Clears accumulated messages when implicit flush is disabled
 *
 * @method static string|null error(string $message)
 * Shows a HTML error message
     *
 * ```php
 * Flash::error("This is an error");
 * ```
 *
 * @method static \Phalcon\Escaper\EscaperInterface getEscaperService()
 * Returns the Escaper Service
 *
 * @method static string|null notice(string $message)
 * Shows a HTML notice/information message
     *
 * ```php
 * Flash::notice("This is an information");
 * ```
 *
 * @method static \Phalcon\Flash\FlashInterface setAutoescape(bool $autoescape)
 * Set the autoescape mode in generated HTML
 *
 * @method static \Phalcon\Flash\FlashInterface setAutomaticHtml(bool $automaticHtml)
 * Set if the output must be implicitly formatted with HTML
 *
 * @method static \Phalcon\Flash\FlashInterface setCssClasses(array $cssClasses)
 * Set an array with CSS classes to format the messages
 *
 * @method static \Phalcon\Flash\FlashInterface setCustomTemplate(string $customTemplate)
 * Set an custom template for showing the messages
 *
 * @method static \Phalcon\Flash\FlashInterface setEscaperService(\Phalcon\Escaper\EscaperInterface $escaperService)
 * Sets the Escaper Service
 *
 * @method static \Phalcon\Flash\FlashInterface setImplicitFlush(bool $implicitFlush)
 * Set whether the output must be implicitly flushed to the output or
 * returned as string
 *
 * @method static string|null success(string $message)
 * Shows a HTML success message
     *
 * ```php
 * Flash::success("The process was finished successfully");
 * ```
 *
 * @method static string|null outputMessage(string $type, $message)
 * Outputs a message formatting it with HTML
     *
 * ```php
 * Flash::outputMessage("error", $message);
 * ```
 *
 * @method static string|null warning(string $message)
 * Shows a HTML warning message
     *
 * ```php
 * Flash::warning("Hey, this is important");
 * ```
 *
 */
class Flash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flash';
    }
}