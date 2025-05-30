<?php

namespace duncan3dc\Sessions;

use duncan3dc\Sessions\Exceptions\InvalidNameException;

use function strlen;

/**
 * A static interface for SessionInstance.
 */
final class Session
{
    /**
     * @var string $name The name of the session.
     */
    private static $name = "";

    /**
     * @var SessionInterface|null $session The underlying session instance.
     */
    private static $session;

    /**
     * Set the name of the session to use.
     *
     * @param string $name The name of the session
     */
    public static function name(string $name): void
    {
        self::$name = $name;
        self::$session = null;
    }


    /**
     * Inject the session instance to use.
     *
     * @param SessionInterface $session The instance to use
     *
     * @return void
     */
    public static function setInstance(SessionInterface $session): void
    {
        self::$session = $session;
    }


    /**
     * Ensure the session instance has been created.
     *
     * @return SessionInterface
     */
    public static function getInstance(): SessionInterface
    {
        if (self::$session instanceof SessionInterface) {
            return self::$session;
        }

        if (strlen(self::$name) < 1) {
            throw new InvalidNameException("Cannot start session, no name has been specified, you must call Session::name() before using this class");
        }

        self::$session = new SessionInstance(self::$name);

        return self::$session;
    }


    /**
     * Create a new namespaced section of this session to avoid clashes.
     *
     * @param string $name The namespace of the session
     *
     * @return SessionInterface
     */
    public static function createNamespace(string $name): SessionInterface
    {
        return self::getInstance()->createNamespace($name);
    }


    /**
     * Get a value from the session data cache.
     *
     * @param string $key The name of the name to retrieve
     *
     * @return mixed
     */
    public static function get(string $key)
    {
        return self::getInstance()->get($key);
    }


    /**
     * Get all the current session data.
     *
     * @return array<string, mixed>
     */
    public static function getAll(): array
    {
        return self::getInstance()->getAll();
    }


    /**
     * Set a value within session data.
     *
     * @param string|array<string, mixed> $data Either the name of the session key to update, or an array of keys to update
     * @param mixed $value If $data is a string then store this value in the session data
     *
     * @return void
     */
    public static function set(string|array $data, $value = null): void
    {
        self::getInstance()->set($data, $value);
    }


    /**
     * This is a convenience method to prevent having to do several checks/set for all persistant variables.
     *
     * If the key name has been passed via POST then that value is stored in the session and returned.
     * If the key name has been passed via GET then that value is stored in the session and returned.
     * If there is already a value in the session data then that is returned.
     * If all else fails then the default value is returned.
     * All checks are truthy/falsy (so a POST value of "0" is ignored), unless the 3rd parameter is set to true.
     *
     * @param string $key The name of the key to retrieve from session data
     * @param mixed $default The value to use if the current session value is falsy
     * @param bool $strict Whether to do strict comparisons or not
     *
     * @return mixed
     */
    public static function getSet(string $key, $default = null, bool $strict = false)
    {
        return self::getInstance()->getSet($key, $default, $strict);
    }


    /**
     * Unset a value within session data.
     *
     * @param string ...$keys The keys to delete from the session
     */
    public static function delete(string ...$keys): void
    {
        self::getInstance()->delete(...$keys);
    }


    /**
     * Clear all previously set values.
     */
    public static function clear(): void
    {
        self::getInstance()->clear();
    }


    /**
     * Retrieve a one-time value from the session data.
     *
     * @param string $key The name of the flash value to retrieve
     *
     * @return mixed
     */
    public static function getFlash(string $key)
    {
        return self::getInstance()->getFlash($key);
    }


    /**
     * Set a one-time value within session data.
     *
     * @param string $key The name of the flash value to update
     * @param mixed $value The value to store against the key
     *
     * @return void
     */
    public static function setFlash(string $key, $value): void
    {
        self::getInstance()->setFlash($key, $value);
    }


    /**
     * Tear down the session and wipe all it's data.
     */
    public static function destroy(): void
    {
        self::getInstance()->destroy();
    }
}
