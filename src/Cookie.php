<?php

namespace duncan3dc\Sessions;

final class Cookie implements CookieInterface
{
    /**
     * @var int The lifetime of the session cookie in seconds.
     */
    private int $lifetime = 0;

    /**
     * @var string Path on the domain where the cookie will work.
     */
    private string $path = "/";

    /**
     * @var string $domain The domain the cookie should be sent to.
     */
    private string $domain = "";

    /**
     * @var bool $secure Only send over secure connections.
     */
    private bool $secure = false;

    /**
     * @var bool $httponly Use the HTTP only flag.
     */
    private bool $httponly = false;


    /**
     * Create a new instance from the current environment settings.
     *
     * @return CookieInterface
     */
    public static function createFromIni(): CookieInterface
    {
        $params = session_get_cookie_params();

        return (new self())
            ->withLifetime($params["lifetime"])
            ->withPath($params["path"])
            ->withDomain($params["domain"])
            ->withSecure($params["secure"])
            ->withHttpOnly($params["httponly"]);
    }


    public function withLifetime(int $lifetime): CookieInterface
    {
        $cookie = clone $this;
        $cookie->lifetime = $lifetime;
        return $cookie;
    }


    public function getLifetime(): int
    {
        return $this->lifetime;
    }


    public function withPath(string $path): CookieInterface
    {
        $cookie = clone $this;
        $cookie->path = $path;
        return $cookie;
    }


    public function getPath(): string
    {
        return $this->path;
    }


    public function withDomain(string $domain): CookieInterface
    {
        $cookie = clone $this;
        $cookie->domain = $domain;
        return $cookie;
    }


    public function getDomain(): string
    {
        return $this->domain;
    }


    public function withSecure(bool $secure): CookieInterface
    {
        $cookie = clone $this;
        $cookie->secure = $secure;
        return $cookie;
    }


    public function isSecure(): bool
    {
        return $this->secure;
    }


    public function withHttpOnly(bool $httponly): CookieInterface
    {
        $cookie = clone $this;
        $cookie->httponly = $httponly;
        return $cookie;
    }


    public function isHttpOnly(): bool
    {
        return $this->httponly;
    }
}
