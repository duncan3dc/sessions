<?php

namespace duncan3dc\Sessions;

class Cookie implements CookieInterface
{
    /**
     * @var int $lifetime The lifetime of the session cookie in seconds.
     */
    private $lifetime = 0;

    /**
     * @var string $path Path on the domain where the cookie will work.
     */
    private $path = "/";

    /**
     * @var string $domain The domain the cookie should be sent to.
     */
    private $domain = "";

    /**
     * @var bool $secure Only send over secure connections.
     */
    private $secure = false;

    /**
     * @var bool $httponly Use the HTTP only flag.
     */
    private $httponly = false;


    /**
     * Create a new instance from the current environment settings.
     *
     * @return CookieInterface
     */
    public static function createFromIni(): CookieInterface
    {
        $params = session_get_cookie_params();

        return (new static)
            ->withLifetime($params["lifetime"])
            ->withPath($params["path"])
            ->withDomain($params["domain"])
            ->withSecure($params["secure"])
            ->withHttpOnly($params["httponly"]);
    }


    /**
     * Create a new instance with the specified lifetime.
     *
     * @param int $lifetime The lifetime of the session cookie in seconds.
     *
     * @return CookieInterface
     */
    public function withLifetime(int $lifetime): CookieInterface
    {
        $cookie = clone $this;
        $cookie->lifetime = $lifetime;
        return $cookie;
    }


    /**
     * Get the current lifetime in seconds.
     *
     * @return int The lifetime of the session cookie in seconds.
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }


    /**
     * Create a new instance with the path.
     *
     * @param string $path Path on the domain where the cookie will work.
     *
     * @return CookieInterface
     */
    public function withPath(string $path): CookieInterface
    {
        $cookie = clone $this;
        $cookie->path = $path;
        return $cookie;
    }


    /**
     * Get the current path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * Create a new instance with the domain.
     *
     * @param string $domain The domain the cookie should be sent to.
     *
     * @return CookieInterface
     */
    public function withDomain(string $domain): CookieInterface
    {
        $cookie = clone $this;
        $cookie->domain = $domain;
        return $cookie;
    }


    /**
     * Get the current domain.
     *
     * @param string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }


    /**
     * Create a new instance with the secure flag.
     *
     * @param bool $secure Only send over secure connections.
     *
     * @return CookieInterface
     */
    public function withSecure(bool $secure): CookieInterface
    {
        $cookie = clone $this;
        $cookie->secure = $secure;
        return $cookie;
    }


    /**
     * Check if this cookie is secure or not.
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }


    /**
     * Create a new instance with the HTTP only flag.
     *
     * @param bool $httponly Use the HTTP only flag.
     *
     * @return CookieInterface
     */
    public function withHttpOnly(bool $httponly): CookieInterface
    {
        $cookie = clone $this;
        $cookie->httponly = (bool) $httponly;
        return $cookie;
    }


    /**
     * Check if this cookie is HTTP only or not.
     *
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httponly;
    }
}
