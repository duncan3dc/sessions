<?php

namespace duncan3dc\Sessions;

interface CookieInterface
{
    /**
     * Create a new instance with the specified lifetime.
     *
     * @param int $lifetime The lifetime of the session cookie in seconds.
     *
     * @return CookieInterface
     */
    public function withLifetime(int $lifetime): CookieInterface;


    /**
     * Get the current lifetime in seconds.
     *
     * @return int The lifetime of the session cookie in seconds.
     */
    public function getLifetime(): int;


    /**
     * Create a new instance with the path.
     *
     * @param string $path Path on the domain where the cookie will work.
     *
     * @return CookieInterface
     */
    public function withPath(string $path): CookieInterface;


    /**
     * Get the current path.
     *
     * @return string
     */
    public function getPath(): string;


    /**
     * Create a new instance with the domain.
     *
     * @param string $domain The domain the cookie should be sent to.
     *
     * @return CookieInterface
     */
    public function withDomain(string $domain): CookieInterface;


    /**
     * Get the current domain.
     *
     * @param string
     */
    public function getDomain(): string;


    /**
     * Create a new instance with the secure flag.
     *
     * @param bool $secure Only send over secure connections.
     *
     * @return CookieInterface
     */
    public function withSecure(bool $secure): CookieInterface;


    /**
     * Check if this cookie is secure or not.
     *
     * @return bool
     */
    public function isSecure(): bool;


    /**
     * Create a new instance with the HTTP only flag.
     *
     * @param bool $httponly Use the HTTP only flag.
     *
     * @return CookieInterface
     */
    public function withHttpOnly(bool $httponly): CookieInterface;


    /**
     * Check if this cookie is HTTP only or not.
     *
     * @return bool
     */
    public function isHttpOnly(): bool;
}
