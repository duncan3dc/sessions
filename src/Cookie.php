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

        return (new static())
            ->withLifetime($params["lifetime"])
            ->withPath($params["path"])
            ->withDomain($params["domain"])
            ->withSecure($params["secure"])
            ->withHttpOnly($params["httponly"]);
    }


    /**
     * @inheritdoc
     */
    public function withLifetime(int $lifetime): CookieInterface
    {
        $cookie = clone $this;
        $cookie->lifetime = $lifetime;
        return $cookie;
    }


    /**
     * @inheritdoc
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }


    /**
     * @inheritdoc
     */
    public function withPath(string $path): CookieInterface
    {
        $cookie = clone $this;
        $cookie->path = $path;
        return $cookie;
    }


    /**
     * @inheritdoc
     */
    public function getPath(): string
    {
        return $this->path;
    }


    /**
     * @inheritdoc
     */
    public function withDomain(string $domain): CookieInterface
    {
        $cookie = clone $this;
        $cookie->domain = $domain;
        return $cookie;
    }


    /**
     * @inheritdoc
     */
    public function getDomain(): string
    {
        return $this->domain;
    }


    /**
     * @inheritdoc
     */
    public function withSecure(bool $secure): CookieInterface
    {
        $cookie = clone $this;
        $cookie->secure = $secure;
        return $cookie;
    }


    /**
     * @inheritdoc
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }


    /**
     * @inheritdoc
     */
    public function withHttpOnly(bool $httponly): CookieInterface
    {
        $cookie = clone $this;
        $cookie->httponly = (bool) $httponly;
        return $cookie;
    }


    /**
     * @inheritdoc
     */
    public function isHttpOnly(): bool
    {
        return $this->httponly;
    }
}
