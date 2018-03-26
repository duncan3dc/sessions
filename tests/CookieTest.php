<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\Cookie;
use PHPUnit\Framework\TestCase;
use function session_set_cookie_params;

class CookieTest extends TestCase
{

    public function testGetLifetime()
    {
        $cookie = new Cookie;
        $this->assertSame(0, $cookie->getLifetime());
    }
    public function testWithLifetime()
    {
        $cookie = new Cookie;
        $this->assertSame(60, $cookie->withLifetime(60)->getLifetime());
        $this->assertSame(0, $cookie->getLifetime());
    }



    public function testGetPath()
    {
        $cookie = new Cookie;
        $this->assertSame("/", $cookie->getPath());
    }
    public function testWithPath()
    {
        $cookie = new Cookie;
        $this->assertSame("/admin", $cookie->withPath("/admin")->getPath());
        $this->assertSame("/", $cookie->getPath());
    }


    public function testGetDomain()
    {
        $cookie = new Cookie;
        $this->assertSame("", $cookie->getDomain());
    }
    public function testWithDomain()
    {
        $cookie = new Cookie;
        $this->assertSame("example.com", $cookie->withDomain("example.com")->getDomain());
        $this->assertSame("", $cookie->getDomain());
    }


    public function testIsSecure()
    {
        $cookie = new Cookie;
        $this->assertSame(false, $cookie->isSecure());
    }
    public function testWithSecure()
    {
        $cookie = new Cookie;
        $this->assertSame(true, $cookie->withSecure(true)->isSecure());
        $this->assertSame(false, $cookie->isSecure());
    }


    public function testIsHttpOnly()
    {
        $cookie = new Cookie;
        $this->assertSame(false, $cookie->isHttpOnly());
    }
    public function testWithHttpOnly()
    {
        $cookie = new Cookie;
        $this->assertSame(true, $cookie->withHttpOnly(true)->isHttpOnly());
        $this->assertSame(false, $cookie->isHttpOnly());
    }


    public function testCreateFromIni()
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
