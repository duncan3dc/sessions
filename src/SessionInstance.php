<?php

namespace duncan3dc\Sessions;

use duncan3dc\Sessions\Exceptions\AlreadyActiveException;
use duncan3dc\Sessions\Exceptions\InvalidNameException;

use function array_key_exists;
use function is_array;
use function session_cache_limiter;
use function session_destroy;
use function session_id;
use function session_name;
use function session_set_cookie_params;
use function session_start;
use function session_write_close;
use function setcookie;
use function strlen;
use function time;

/**
 * A non-blocking session manager.
 */
class SessionInstance implements SessionInterface
{
    use SessionTrait;

    /**
     * @var bool $init Whether the session has been started or not.
     */
    private $init = false;

    /**
     * @var string $name The name of the session.
     */
    private $name = "";

    /**
     * @var array<string, mixed> $data The cache of the session data.
     */
    private $data = [];

    /**
     * @var string The session ID
     */
    private $id = "";

    /**
     * @var CookieInterface $cookie The cookie settings to use.
     */
    private $cookie;


    /**
     * Create a new instance.
     *
     * @param string $name The name of the session
     * @param CookieInterface $cookie The cookie settings to use
     * @param string $id The session ID to use
     */
    public function __construct(string $name, CookieInterface $cookie = null, string $id = "")
    {
        if (strlen($name) < 1) {
            throw new InvalidNameException("Cannot start session, no name has been specified");
        }

        if ($cookie === null) {
            $cookie = Cookie::createFromIni();
        }

        $this->name = $name;
        $this->cookie = $cookie;
        $this->id = $id;
    }


    /**
     * Ensure the session data is loaded into cache.
     *
     * @return void
     * @throws AlreadyActiveException
     */
    private function init()
    {
        if ($this->init) {
            return;
        }
        $this->init = true;

        if (session_status() === \PHP_SESSION_ACTIVE) {
            throw new AlreadyActiveException("A session has already been started");
        }

        session_cache_limiter("");

        session_set_cookie_params($this->cookie->getLifetime(), $this->cookie->getPath(), $this->cookie->getDomain(), $this->cookie->isSecure(), $this->cookie->isHttpOnly());

        session_name($this->name);

        if ($this->id !== "") {
            session_id($this->id);
        }

        session_start([
            "read_and_close"    =>  true,
        ]);

        /**
         * If the cookie has a specific lifetime (not unlimited)
         * then ensure it is extended on each use of the session.
         */
        if ($this->cookie->getLifetime() > 0) {
            $expires = time() + $this->cookie->getLifetime();
            setcookie($this->name, (string) session_id(), $expires, $this->cookie->getPath(), $this->cookie->getDomain(), $this->cookie->isSecure(), $this->cookie->isHttpOnly());
        }

        # Grab the sessions data to respond to get()
        $this->data = $_SESSION;

        # Grab session ID
        $this->id = (string) session_id();
    }


    /**
     * Get the session ID.
     *
     * @return string
     * @throws AlreadyActiveException
     */
    public function getId(): string
    {
        $this->init();

        return $this->id;
    }


    /**
     * Update the current session id with a newly generated one.
     *
     * @return string The new session ID
     * @throws AlreadyActiveException
     */
    public function regenerate()
    {
        $this->init();

        # Generate a new session
        session_start();
        session_regenerate_id();

        # Get the newly generated ID
        $this->id = (string) session_id();

        # Remove the lock from the session file
        session_write_close();

        return $this->id;
    }


    /**
     * Create a new namespaced section of this session to avoid clashes.
     *
     * @param string $name The namespace of the session
     *
     * @return SessionInterface
     */
    public function createNamespace(string $name): SessionInterface
    {
        return new SessionNamespace($name, $this);
    }


    /**
     * Get a value from the session data cache.
     *
     * @param string $key The name of the name to retrieve
     *
     * @return mixed
     * @throws AlreadyActiveException
     */
    public function get(string $key)
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
     * @throws AlreadyActiveException
     */
    public function getAll(): array
    {
        $this->init();

        return $this->data;
    }


    /**
     * Set a value within session data.
     *
     * @throws AlreadyActiveException
     */
    public function set($data, $value = null): SessionInterface
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
     * @return SessionInterface
     */
    public function destroy(): SessionInterface
    {
        try {
            $this->init();
        } catch (AlreadyActiveException $exception) {
        }

        # Start the session up, but ignore the error about headers already being sent
        @session_start();

        # Clear the session data from the server
        session_destroy();

        # Clear the cookie so the client knows the session is gone
        setcookie($this->name, "", time() - 86400, $this->cookie->getPath(), $this->cookie->getDomain(), $this->cookie->isSecure(), $this->cookie->isHttpOnly());

        # Reset the session data
        $this->init = false;
        $this->data = [];

        return $this;
    }
}
