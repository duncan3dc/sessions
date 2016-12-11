<?php

namespace duncan3dc\SessionsTest;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class WebTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileCookieJar */
    private $cookieJar;
    /** @var string */
    private $cookies;
    /** @var Client */
    private $client;

    public function setUp()
    {
        # HHVM no longer has a built in webserver, so don't run these tests
        if (isset($_ENV["TRAVIS_PHP_VERSION"]) && $_ENV["TRAVIS_PHP_VERSION"] === "hhvm") {
            $this->markTestSkipped("No internal webserver available on HHVM for web tests");
        }

        $this->cookies = tempnam(sys_get_temp_dir(), "duncan3dc-sessions-");
        $this->cookieJar = new FileCookieJar($this->cookies);

        $this->client = new Client([
            "cookies" => $this->cookieJar,
        ]);
    }


    public function tearDown()
    {
        unset($this->client);

        unlink($this->cookies);
    }


    private function request($path)
    {
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


    public function testCookies()
    {
        $response = $this->request("getall.php");
        $cookie = $response->getHeader("Set-Cookie")[0];
        $this->assertRegExp("/^web=[a-z0-9]+; path=\/$/", $cookie);
    }

    public function testDelete()
    {
        $this->request("set.php?key=ok&value=no");
        $this->request("delete.php");

        $this->assertSame(1, $this->cookieJar->count());
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
        ], $this->cookieJar->toArray()[0]);
    }

    public function testCookieWithParams()
    {
        $response = $this->request("sub/getall.php");
        $cookie = $response->getHeader("Set-Cookie")[0];
        $this->assertRegExp(
            "/^web=[a-z0-9]+; expires=.* GMT; Max-Age=3600; path=\/sub; domain=localhost; HttpOnly$/",
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

        $this->assertSame(1, $this->cookieJar->count());
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
        ], $this->cookieJar->toArray()[0]);
    }

    public function testRefreshCookie()
    {
        $this->request("sub/set.php?key=ok&value=yes");
        $response = $this->request("sub/getall.php");

        $cookie = $response->getHeader("Set-Cookie")[0];
        $this->assertRegExp(
            "/^web=[a-z0-9]+; expires=.* GMT; Max-Age=3600; path=\/sub; domain=localhost; HttpOnly$/",
            $cookie
        );
    }
}
