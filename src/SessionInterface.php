<?php

namespace duncan3dc\Sessions;

/**
 * A generic interface for session managers.
 */
interface SessionInterface
{
    /**
     * Create a new namespaced section of this session to avoid clashes.
     *
     * @param string $name The namespace of the session
     *
     * @return SessionInterface
     */
    public function createNamespace(string $name): SessionInterface;


    /**
     * Get a value from the session data cache.
     *
     * @param string $key The name of the name to retrieve
     *
     * @return mixed
     */
    public function get(string $key);


    /**
     * Get all the current session data.
     *
     * @return array<string, mixed>
     */
    public function getAll(): array;


    /**
     * Set a value within session data.
     *
     * @param string|array<string, mixed> $data Either the name of the session key to update, or an array of keys to update
     * @param mixed $value If $data is a string then store this value in the session data
     *
     * @return SessionInterface
     */
    public function set(string|array $data, $value = null): SessionInterface;


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
    public function getSet(string $key, $default = null, bool $strict = false);


    /**
     * Unset a value within session data.
     *
     * @param string ...$keys The name of the keys to delete
     *
     * @return SessionInterface
     */
    public function delete(string ...$keys): SessionInterface;


    /**
     * Clear all previously set values.
     *
     * @return SessionInterface
     */
    public function clear(): SessionInterface;


    /**
     * Retrieve a one-time value from the session data.
     *
     * @param string $key The name of the flash value to retrieve
     *
     * @return mixed
     */
    public function getFlash(string $key);


    /**
     * Set a one-time value within session data.
     *
     * @param string $key The name of the flash value to update
     * @param mixed $value The value to store against the key
     *
     * @return SessionInterface
     */
    public function setFlash(string $key, $value): SessionInterface;


    /**
     * Tear down the session and wipe all its data.
     */
    public function destroy(): void;
}
