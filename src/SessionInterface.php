<?php

namespace duncan3dc\Sessions;

/**
 * A generic interface for session managers.
 */
interface SessionInterface
{


    /**
     * Get a value from the session data cache.
     *
     * @param string $key The name of the name to retrieve
     *
     * @return mixed
     */
    public function get($key);


    /**
     * Set a value within session data.
     *
     * @param string|array $data Either the name of the session key to update, or an array of keys to update
     * @param mixed $value If $data is a string then store this value in the session data
     *
     * @return static
     */
    public function set($data, $value = null);


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
    public function getSet($key, $default = null, $strict = false);


    /**
     * Unset a value within session data.
     *
     * @param string $key The name of the session key to delete
     *
     * @return static
     */
    public function delete(...$keys);


    /**
     * Clear all previously set values.
     *
     * @return static
     */
    public function clear();
}
