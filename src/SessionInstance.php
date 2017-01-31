<?php

namespace duncan3dc\Sessions;

/**
 * A non-blocking session manager.
 */
class SessionInstance implements SessionInterface
{
    use SessionTrait;

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
     * @var Cookie $cookie The cookie settings to use.
     */
    private $cookie;


    /**
     * Create a new instance.
     *
     * @param string $name The name of the session
     * @param Cookie $cookie The cookie settings to use
     */
    public function __construct($name, Cookie $cookie = null)
    {
        if (strlen($name) < 1) {
            throw new \InvalidArgumentException("Cannot start session, no name has been specified");
        }

        if ($cookie === null) {
            $cookie = Cookie::createFromIni();
        }

        $this->name = $name;
        $this->cookie = $cookie;
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

        session_set_cookie_params($this->cookie->getLifetime(), $this->cookie->getPath(), $this->cookie->getDomain(), $this->cookie->isSecure(), $this->cookie->isHttpOnly());

        session_name($this->name);

        session_start();

        $cookieParams = session_get_cookie_params();
        if ($cookieParams['lifetime'] > 0) {
            setcookie($this->name, session_id(), time() + $cookieParams['lifetime'], $cookieParams['path'],
                $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
        }

        # Grab the sessions data to respond to get()
        $this->data = $_SESSION;

        # Remove the lock from the session file
        session_write_close();
    }


    /**
     * Create a new namespaced section of this session to avoid clashes.
     *
     * @param string $name The namespace of the session
     *
     * @return SessionNamespace
     */
    public function createNamespace($name)
    {
        return new SessionNamespace($name, $this);
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
     * Get all the current session data.
     *
     * @return array
     */
    public function getAll()
    {
        $this->init();

        return $this->data;
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
     * Tear down the session and wipe all its data.
     *
     * @return static
     */
    public function destroy()
    {
        $this->init();

        # Remove the session cookie
        $cookieParams = session_get_cookie_params();
        setcookie($this->name, "", 1, $cookieParams['path'],
            $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);

        # destroy the session
        session_destroy();

        # Reset the session data
        session_unset();
        $this->init = false;
        $this->data = [];

        return $this;
    }
}
