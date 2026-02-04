<?php
namespace Core\Facades;

/**
 * Class Debug
 * @package Core\Facades
 * Provides debug capabilities to Phalcon applications
 * 
 * @method static \Phalcon\Debug clearVars()
 * Clears are variables added previously
 *
 * @method static \Phalcon\Debug debugVar($varz, string $key = null)
 * Adds a variable to the debug output
 *
 * @method static string getCssSources()
 * Returns the CSS sources
 *
 * @method static string getJsSources()
 * Returns the JavaScript sources
 *
 * @method static string getVersion()
 * Generates a link to the current version documentation
 *
 * @method static void halt()
 * Halts the request showing a backtrace
 *
 * @method static \Phalcon\Debug listen(bool $exceptions = true, bool $lowSeverity = false)
 * Listen for uncaught exceptions and unsilent notices or warnings
 *
 * @method static \Phalcon\Debug listenExceptions()
 * Listen for uncaught exceptions
 *
 * @method static \Phalcon\Debug listenLowSeverity()
 * Listen for unsilent notices or warnings
 *
 * @method static bool onUncaughtException(\Throwable $exception)
 * Handles uncaught exceptions
 *
 * @method static void onUncaughtLowSeverity($severity, $message, $file, $line, $context)
 * Throws an exception when a notice or warning is raised
 *
 * @method static \Phalcon\Debug setBlacklist(array $blacklist)
 * Sets if files the exception's backtrace must be showed
 *
 * @method static \Phalcon\Debug setShowBackTrace(bool $showBackTrace)
 * Sets if files the exception's backtrace must be showed
 *
 * @method static \Phalcon\Debug setShowFileFragment(bool $showFileFragment)
 * Sets if files must be completely opened and showed in the output
 * or just the fragment related to the exception
 *
 * @method static \Phalcon\Debug setShowFiles(bool $showFiles)
 * Set if files part of the backtrace must be shown in the output
 *
 * @method static \Phalcon\Debug setUri(string $uri)
 * Change the base URI for static resources
 *
 * @method static string renderHtml(\Throwable $exception)
 *
 */
class Debug extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'debug';
    }
}