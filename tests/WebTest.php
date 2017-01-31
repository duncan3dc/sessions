<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\ObjectIntruder\Intruder;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SetCookie;

class WebTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileCookieJar */
    private $cookies;
    /** @var Client */
    private $client;

    private function getCookie($name)
    {
        /** @var SetCookie $cookie */
        foreach ($this->cookies->getIterator() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }
    }

    public function setUp()
    {
        # HHVM no longer has a built in webserver, so don't run these tests
        if (isset($_ENV["TRAVIS_PHP_VERSION"]) && $_ENV["TRAVIS_PHP_VERSION"] === "hhvm") {
            $this->markTestSkipped("No internal webserver available on HHVM for web tests");
        }

        $path = tempnam(sys_get_temp_dir(), "duncan3dc-sessions-");
        $this->cookies = new FileCookieJar($path);

        $this->client = new Client([
            "cookies" => $this->cookies,
        ]);
    }


    public function tearDown()
    {
        unset($this->client);

        $cookies = new Intruder($this->cookies);
        unlink($cookies->filename);
    }


    private function request($path, $name = null)
    {
        if ($name !== null) {
            if (strpos($path, "?")) {
                $path .= "&";
            } else {
                $path .= "?";
            }
            $path .= "session_name={$name}";
        }

        return $this->client->request("GET", "http://localhost:" . SERVER_PORT . "/{$path}");
    }


    private function assertRequest($request, array $expected)
    {
        $response = $this->request($request);
        $body = (string) $response->getBody();
        $result = unserialize($body);

        $this->assertSame($expected, $result);
    }


    public function testGetEmpty()
    {
        $this->assertRequest("getall.php", []);
    }


    public function testSetSomething()
    {
        $this->request("set.php?key=ok&value=yep");
        $this->assertRequest("getall.php", [
            "ok"    =>  "yep",
        ]);
    }


    public function testDestroy1()
    {
        $this->request("set.php?key=ok&value=yep");
        $this->assertRequest("getall.php", [
            "ok"    =>  "yep",
        ]);

        $this->request("destroy.php");
        $this->assertRequest("getall.php", []);
    }
    public function testDestroy2()
    {
        $this->request("set.php?key=ok&value=web1", "web1");
        $this->assertRequest("getall.php?session_name=web1", [
            "ok"    =>  "web1",
        ]);

        $this->request("set.php?key=ok&value=web2", "web2");
        $this->assertRequest("getall.php?session_name=web2", [
            "ok"    =>  "web2",
        ]);

        # Make sure that destroy only wipes the correct session
        $this->request("destroy.php", "web1");
        $this->assertRequest("getall.php?session_name=web1", []);
        $this->assertRequest("getall.php?session_name=web2", [
            "ok"    =>  "web2",
        ]);
    }

    public function testDestroyEmptiesSession()
    {
        $this->request("set.php?key=ok&value=web1", "web1");
        $this->assertRequest("getall.php?session_name=web1", [
            "ok"    =>  "web1",
        ]);

        # destroy the session but keep the cookie (malfunc client)
        /** @var SetCookie $cookie */
        $cookie = clone $this->getCookie("web1");
        $this->request("destroy.php", "web1");
        $this->cookies->setCookie($cookie);

        $this->assertRequest("getall.php?session_name=web1", []);
    }

    public function testDestroyRemovesSession()
    {
        // remove all files
        exec('rm -Rf /tmp/duncan3dc-sessions/*');

        $this->request("set.php?key=ok&value=web1", "web1");
        $this->assertRequest("getall.php?session_name=web1", [
            "ok"    =>  "web1",
        ]);

        $this->request("destroy.php", "web1");
        exec('find /tmp/duncan3dc-sessions -type f', $files);
        $this->assertEmpty($files);
    }


    public function testCookies()
    {
        $this->request("cookies.php");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals("web", $cookie["Name"]);
        $this->assertEquals("localhost", $cookie["Domain"]);
        $this->assertEquals("/", $cookie["Path"]);
        $this->assertEquals(0, $cookie["Max-Age"]);
        $this->assertEquals(false, $cookie["Secure"]);
        $this->assertEquals(false, $cookie["HttpOnly"]);
    }
    public function testCookieLifetime()
    {
        $this->request("cookies.php?lifetime=33");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals(33, $cookie["Max-Age"]);
    }
    public function testCookiePath()
    {
        $this->request("cookies.php?path=/admin");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals("/admin", $cookie["Path"]);
    }
    public function testCookieDomain()
    {
        $this->request("cookies.php?domain=example.com");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals("example.com", $cookie["Domain"]);
    }
    public function testCookieSecure()
    {
        $this->request("cookies.php?secure=1");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals(true, $cookie["Secure"]);
    }
    public function testCookieHttpOnly()
    {
        $this->request("cookies.php?httponly=1");

        $cookie = $this->cookies->toArray()[0];

        $this->assertEquals(true, $cookie["HttpOnly"]);
    }

    public function testDelete()
    {
        $this->request("set.php?key=ok&value=no");
        $this->request("delete.php");

        $this->assertSame(1, $this->cookies->count());
        $this->assertEquals([
            'Name' => 'web',
            'Value' => 'deleted',
            'Domain' => 'localhost',
            'Path' => '/',
            'Max-Age' => '0',
            'Expires' => 1,
            'Secure' => false,
            'Discard' => false,
            'HttpOnly' => false
        ], $this->cookies->toArray()[0]);
    }

    public function testCookieWithParams()
    {
        $response = $this->request("sub/getall.php");
        $cookie = $response->getHeader("Set-Cookie")[0];
        $this->assertRegExp(
            "/^web=[a-z0-9]+; expires=.* GMT; Max-Age=3600; path=\/sub; domain=localhost; [Hh]ttp[Oo]nly$/",
            $cookie
        );
    }

    public function testSubIsNotAvailableInParent()
    {
        $this->request("sub/set.php?key=ok&value=no");
        $this->assertRequest("getall.php", []);
    }

    public function testDeleteSub()
    {
        $this->request("sub/set.php?key=ok&value=no");
        $this->request("sub/delete.php");

        $this->assertSame(1, $this->cookies->count());
        $this->assertEquals([
            'Name' => 'web',
            'Value' => 'deleted',
            'Domain' => 'localhost',
            'Path' => '/sub',
            'Max-Age' => '0',
            'Expires' => 1,
            'Secure' => false,
            'Discard' => false,
            'HttpOnly' => true
        ], $this->cookies->toArray()[0]);
    }

    public function testRefreshCookie()
    {
        $this->request("sub/set.php?key=ok&value=yes");
        $response = $this->request("sub/getall.php");

        $cookie = $response->getHeader("Set-Cookie")[0];
        $this->assertRegExp(
            "/^web=[a-z0-9]+; expires=.* GMT; Max-Age=3600; path=\/sub; domain=localhost; [Hh]ttp[Oo]nly$/",
            $cookie
        );
    }
}
