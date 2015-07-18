<?php

namespace duncan3dc\Sessions;

/**
 * A non-blocking session manager.
 */
class SessionInstance implements SessionInterface
{
    /**
     * @var bool $init Whether the session has been started or not.
     */
    protected $init = false;

    /**
     * @var string $name The name of the session.
     */
    protected $name = "";

    /**
     * @var array $data The cache of the session data.
     */
    protected $data = [];


    /**
     * Create a new instance.
     *
     * @param string $name The name of the session
     */
    public function __construct($name)
    {
        if (strlen($name) < 1) {
            throw new \InvalidArgumentException("Cannot start session, no name has been specified");
        }

        $this->name = $name;
    }


    /**
     * Ensure the session data is loaded into cache.
     *
     * @return void
     */
    protected function init()
    {
        if ($this->init) {
            return;
        }
        $this->init = true;

        session_cache_limiter(false);

        session_name($this->name);

        session_start();

        # Grab the sessions data to respond to get()
        $this->data = $_SESSION;

        # Remove the lock from the session file
        session_write_close();
    }


    /**
     * Get a value from the session data cache.
     *
     * @param string $key The name of the name to retrieve
     *
     * @return mixed
     */
    public function get($key)
    {
        $this->init();

        if (!array_key_exists($key, $this->data)) {
            return;
        }

        return $this->data[$key];
    }


    /**
     * Set a value within session data.
     *
     * @param string|array $data Either the name of the session key to update, or an array of keys to update
     * @param mixed $value If $data is a string then store this value in the session data
     *
     * @return static
     */
    public function set($data, $value = null)
    {
        $this->init();

        # Check that at least one value has been changed before starting up the sesson
        $changed = false;
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($this->get($key) !== $val) {
                    $changed = true;
                    break;
                }
            }
        } else {
            if ($this->get($data) !== $value) {
                $changed = true;
            }
        }

        # If none of the values have changed then don't write to session data
        if (!$changed) {
            return $this;
        }

        /**
         * Whenever a key is set, we need to start the session up again to store it
         * When session_start is called it attempts to send the cookie to the browser with the session id in.
         * However if some output has already been sent then this will fail, this is why we suppress errors on the call here
         */
        @session_start();

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $_SESSION[$key] = $val;
            }
        } else {
            $_SESSION[$data] = $value;
        }

        $this->data = $_SESSION;

        session_write_close();

        return $this;
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
    public function getSet($key, $default = null, $strict = false)
    {
        $this->init();

        # If this key was just submitted via post then store it in the session data
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            if ($strict || $value) {
                $this->set($key, $value);
                return $value;
            }
        }

        # If this key is part of the get data then store it in session data
        if (isset($_GET[$key])) {
            $value = $_GET[$key];
            if ($strict || $value) {
                $this->set($key, $value);
                return $value;
            }
        }

        # Get the current value for this key from session data
        $value = $this->get($key);

        # If there is no current value for this key then set it to the supplied default
        if ($default !== null) {
            if (($strict && $value === null) || (!$strict && !$value)) {
                $value = $default;
                $this->set($key, $value);
            }
        }

        return $value;
    }


    /**
     * Unset a value within session data.
     *
     * @param string $key The name of the session key to delete
     *
     * @return static
     */
    public function delete(...$keys)
    {
        $keyValues = [];
        foreach ($keys as $key) {
            $keyValues[$key] = null;
        }

        return $this->set($keyValues);
    }


    /**
     * Clear all previously set values.
     *
     * @return static
     */
    public function clear()
    {
        $this->delete(...array_keys($this->data));

        return $this;
    }


    /**
     * Tear down the session and wipe all its data.
     *
     * @return static
     */
    public function destroy()
    {
        @session_start();

        unset($_SESSION);

        setcookie($this->name, "", time() - 86400, "/");

        session_destroy();

        # Reset the session data
        $this->init = false;
        $this->data = [];

        return $this;
    }
}
