<?php
namespace Core\Facades;

/**
 * Class Session
 * @package Core\Facades
 * Session manager class
 * 
 * @method static void destroy()
 * Destroy/end a session
 * 
 * @method static bool exists()
 * Check whether the session has been started
 * 
 * @method static mixed get(string $key, $defaultValue = null, bool $remove = false)
 * Gets a session variable from an application context
 * 
 * @method static \SessionHandlerInterface getAdapter()
 * Returns the stored session adapter
 * 
 * @method static string getId()
 * Returns the session id
 * 
 * @method static string getName()
 * Returns the name of the session
 * 
 * @method static bool has(string $key)
 * Check whether a session variable is set in an application context
 * 
 * @method static array getOptions()
 * Get internal options
 * 
 * @method static \Phalcon\Session\ManagerInterface regenerateId($deleteOldSession = true)
 * Regenerates the session id using the adapter.
 * 
 * @method static void remove(string $key)
 * Removes a session variable from an application context
 * 
 * @method static void set(string $key, $value)
 * Sets a session variable in an application context
 * 
 * @method static \Phalcon\Session\ManagerInterface setAdapter(\SessionHandlerInterface $adapter)
 * Set the adapter for the session
 * 
 * @method static \Phalcon\Session\ManagerInterface setId(string $id)
 * Set session Id
 * 
 * @method static \Phalcon\Session\ManagerInterface setName(string $name)
 * Set the session name. Throw exception if the session has started
 * and do not allow poop names
 * 
 * @method static void setOptions(array $options)
 * Sets session's options
 * 
 * @method static bool start()
 * Starts the session (if headers are already sent the session will not be
 * started)
 * 
 * @method static int status()
 * Returns the status of the current session.
 */
class Session extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}