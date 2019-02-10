<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\Cookie;
use PHPUnit\Framework\TestCase;
use function session_set_cookie_params;

class CookieTest extends TestCase
{

    public function testGetLifetime(): void
    {
        $cookie = new Cookie();
        $this->assertSame(0, $cookie->getLifetime());
    }
    public function testWithLifetime(): void
    {
        $cookie = new Cookie();
        $this->assertSame(60, $cookie->withLifetime(60)->getLifetime());
        $this->assertSame(0, $cookie->getLifetime());
    }



    public function testGetPath(): void
    {
        $cookie = new Cookie();
        $this->assertSame("/", $cookie->getPath());
    }
    public function testWithPath(): void
    {
        $cookie = new Cookie();
        $this->assertSame("/admin", $cookie->withPath("/admin")->getPath());
        $this->assertSame("/", $cookie->getPath());
    }


    public function testGetDomain(): void
    {
        $cookie = new Cookie();
        $this->assertSame("", $cookie->getDomain());
    }
    public function testWithDomain(): void
    {
        $cookie = new Cookie();
        $this->assertSame("example.com", $cookie->withDomain("example.com")->getDomain());
        $this->assertSame("", $cookie->getDomain());
    }


    public function testIsSecure(): void
    {
        $cookie = new Cookie();
        $this->assertSame(false, $cookie->isSecure());
    }
    public function testWithSecure(): void
    {
        $cookie = new Cookie();
        $this->assertSame(true, $cookie->withSecure(true)->isSecure());
        $this->assertSame(false, $cookie->isSecure());
    }


    public function testIsHttpOnly(): void
    {
        $cookie = new Cookie();
        $this->assertSame(false, $cookie->isHttpOnly());
    }
    public function testWithHttpOnly(): void
    {
        $cookie = new Cookie();
        $this->assertSame(true, $cookie->withHttpOnly(true)->isHttpOnly());
        $this->assertSame(false, $cookie->isHttpOnly());
    }


    public function testCreateFromIni(): void
    {
        session_set_cookie_params(25, "/users", "example.com", false, true);
        $cookie = Cookie::createFromIni();
        $this->assertSame(25, $cookie->getLifetime());
        $this->assertSame("/users", $cookie->getPath());
        $this->assertSame("example.com", $cookie->getDomain());
        $this->assertSame(false, $cookie->isSecure());
        $this->assertSame(true, $cookie->isHttpOnly());
    }
}
