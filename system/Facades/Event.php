<?php
namespace Core\Facades;

/**
 * Class Event
 * @package Core\Facades
 * Dispatches events to registered listeners.
 * 
 * @method static void attach(string $eventType, $handler, int $priority = self::DEFAULT_PRIORITY)
 * Attach a listener to the events manager
 * 
 * @method static bool arePrioritiesEnabled()
 * Returns if priorities are enabled
 * 
 * @method static void collectResponses(bool $collect)
 * Tells the event manager if it needs to collect all the responses returned
 * by every registered listener in a single fire
 * 
 * @method static void detach(string $eventType, $handler)
 * Detach the listener from the events manager
 * 
 * @method static void detachAll(string $type = null)
 * Removes all events from the EventsManager
 * 
 * @method static void enablePriorities(bool $enablePriorities)
 * Set if priorities are enabled in the EventsManager.
 *
 * A priority queue of events is a data structure similar
 * to a regular queue of events: we can also put and extract
 * elements from it. The difference is that each element in a
 * priority queue is associated with a value called priority.
 * This value is used to order elements of a queue: elements
 * with higher priority are retrieved before the elements with
 * lower priority.
 * 
 * @method static mixed fire(string $eventType, $source, $data = null, bool $cancelable = true)
 * Fires an event in the events manager causing the active listeners to be
 * notified about it
 *
 * ```php
 * $eventsManager->fire("db", $connection);
 * ```
 * 
 * @method static mixed fireQueue(\SplPriorityQueue $queue, EventInterface $event)
 * Internal handler to call a queue of events
 * 
 * @method static array getListeners(string $type)
 * Returns all the attached listeners of a certain type
 * 
 * @method static array getResponses()
 * Returns all the responses returned by every handler executed by the last
 * 'fire' executed
 * 
 * @method static bool hasListeners(string $type)
 * Check whether certain type of event has listeners
 * 
 * @method static bool isCollecting()
 * Check if the events manager is collecting all all the responses returned
 * by every registered listener in a single fire
 * 
 * @method static bool isValidHandler($handler)
 */
class Event extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eventsManager';
    }
}