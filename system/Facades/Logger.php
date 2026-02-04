<?php
namespace Core\Facades;

/**
 * Class Logger
 * @package Core\Facades
 * This component offers logging capabilities for your application. The
 * component accepts multiple adapters, working also as a multiple logger.
 * Phalcon\Logger implements PSR-3.
 * 
 * @method static int getLogLevel()
 * Minimum log level for the logger
 * 
 * @method static \Phalcon\Logger addAdapter(string $name, \Phalcon\Logger\Adapter\AdapterInterface $adapter)
 * Add an adapter to the stack. For processing we use FIFO
 * 
 * @method static void alert($message, array $context = array())
 * Action must be taken immediately.
 *
 * Example: Entire website down, database unavailable, etc. This should
 * trigger the SMS alerts and wake you up.
 * 
 * @method static void critical($message, array $context = array())
 * Critical conditions.
 *
 * Example: Application component unavailable, unexpected exception.
 * 
 * @method static void debug($message, array $context = array())
 * Detailed debug information.
 * 
 * @method static void error($message, array $context = array())
 * Runtime errors that do not require immediate action but should typically
 * be logged and monitored.
 * 
 * @method static void emergency($message, array $context = array())
 * System is unusable.
 * 
 * @method static \Phalcon\Logger excludeAdapters(array $adapters = array())
 * Exclude certain adapters.
 * 
 * @method static \Phalcon\Logger\Adapter\AdapterInterface getAdapter(string $name)
 * Returns an adapter from the stack
 * 
 * @method static array getAdapters()
 * Returns the adapter stack array
 * 
 * @method static string getName()
 * Returns the name of the logger
 * 
 * @method static void info($message, array $context = array())
 * Interesting events.
 *
 * Example: User logs in, SQL logs.
 * 
 * @method static void log($level, $message, array $context = array())
 * Logs with an arbitrary level.
 * 
 * @method static void notice($message, array $context = array())
 * Normal but significant events.
 * 
 * @method static \Phalcon\Logger removeAdapter(string $name)
 * Removes an adapter from the stack
 * 
 * @method static \Phalcon\Logger setAdapters(array $adapters)
 * Sets the adapters stack overriding what is already there
 * 
 * @method static \Phalcon\Logger setLogLevel(int $level)
 * Sets the log level above which we can log
 * 
 * @method static void warning($message, array $context = array())
 * Exceptional occurrences that are not errors.
 *
 * Example: Use of deprecated APIs, poor use of an API, undesirable things
 * that are not necessarily wrong.
 * 
 */
class Logger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'logger';
    }
}