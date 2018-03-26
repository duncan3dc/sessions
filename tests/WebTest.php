<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\ObjectIntruder\Intruder;
use duncan3dc\Sessions\SessionInstance;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SetCookie;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function exec;
use function session_set_save_handler;
use function sleep;
use function strpos;
use function sys_get_temp_dir;
use function tempnam;
use function time;
use function unlink;
use function unserialize;

class WebTest extends TestCase
{
    const SERVER_PORT = 15377;
    private static $pid;

    /**
     * @var CookieJarInterface $cookies The cookies from the latest response.
     */
    private $cookies;

    /**
     * @var ClientInterface $client A HTTP client to use for testing.
     */
    private $client;


    public static function setUpBeforeClass()
    {
        # Start the internal web server for cookie based tests
        exec("php -S localhost:" . self::SERVER_PORT . " -t " . __DIR__ . "/web >/dev/null 2>&1 & echo $!", $output, $status);
        self::$pid = (int) $output[0];

        # Give the server a second to start up
        sleep(1);
    }


    public static function tearDownAfterClass()
    {
        # Ensure the internal web server is killed when the tests end
        exec("kill " . self::$pid);
    }


    public function setUp()
    {
        session_set_save_handler(new \SessionHandler);

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


    /**
     * Get a cookie from the latest response
     *
     * @param string $name The name of the cookie to get
     *
     * @return SetCookie
     */
    private function getCookie($name = "web"): SetCookie
    {
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }
    }


    /**
     * Send a request to the testing backend.
     *
     * @param string $path The path to send the request to
     * @param ?string $name The name of the session to use
     *
     * @return ResponseInterface
     */
    private function request(string $path, string $name = null): ResponseInterface
    {
        if ($name !== null) {
            if (strpos($path, "?")) {
                $path .= "&";
            } else {
                $path .= "?";
            }
            $path .= "session_name={$name}";
        }

        return $this->client->request("GET", "http://localhost:" . self::SERVER_PORT . "/{$path}");
    }


    /**
     * Send a request to the testing backend and compare the response.
     *
     * @param string $path The path to send the request to
     * @param array $expected The expected response data
     *
     * @return void
     */
    private function assertRequest(string $path, array $expected): void
    {
        $response = $this->request($path);
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


    public function testDestroy()
    {
        $this->request("set.php?key=ok&value=yep");
        $this->assertRequest("getall.php", [
            "ok"    =>  "yep",
        ]);

        $this->request("destroy.php");
        $this->assertRequest("getall.php", []);
    }


    public function testDestroyCorrectsession()
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


    public function testDestroyCookie()
    {
        $this->request("destroy.php");

        $cookie = $this->getCookie();

        $this->assertEquals("web", $cookie->getName());
        $this->assertEquals("deleted", $cookie->getValue());
        $this->assertLessThan(time(), $cookie->getExpires());
    }


    public function testDestroyEmptiesSession()
    {
        $this->request("set.php?key=ok&value=yep");
        $this->assertRequest("getall.php", [
            "ok"    =>  "yep",
        ]);

        # Destroy the session but keep the cookie (malfunctioning client)
        foreach ($this->cookies as $cookie) {
            if ($cookie->getName() === "web") {
                $sessionCookie = $cookie;
                break;
            }
        }

        $this->request("destroy.php");
        $this->cookies->setCookie($sessionCookie);

        $this->assertRequest("getall.php", []);
    }


    public function testCookies()
    {
        $this->request("cookies.php");

        $cookie = $this->getCookie();

        $this->assertEquals("web", $cookie->getName());
        $this->assertEquals("localhost", $cookie->getDomain());
        $this->assertEquals("/", $cookie->getPath());
        $this->assertEquals(0, $cookie->getMaxAge());
        $this->assertEquals(false, $cookie->getSecure());
        $this->assertEquals(false, $cookie->getHttpOnly());
    }
    public function testCookieLifetime()
    {
        $this->request("cookies.php?lifetime=33");
        $this->assertEquals(33, $this->getCookie()->getMaxAge());
    }
    public function testCookiePath()
    {
        $this->request("cookies.php?path=/admin");
        $this->assertEquals("/admin", $this->getCookie()->getPath());
    }
    public function testCookieDomain()
    {
        $this->request("cookies.php?domain=example.com");
        $this->assertEquals("example.com", $this->getCookie()->getDomain());
    }
    public function testCookieSecure()
    {
        $this->request("cookies.php?secure=1");
        $this->assertEquals(true, $this->getCookie()->getSecure());
    }
    public function testCookieHttpOnly()
    {
        $this->request("cookies.php?httponly=1");
        $this->assertEquals(true, $this->getCookie()->getHttpOnly());
    }


    public function testSessionIDReuse()
    {
        $response = $this->request("use-id.php?session_name=web-sockets&key=using&value=ID");
        $id = (string) $response->getBody();

        # Ensure that we can retrieve values here that were set via the web browser
        $session = new SessionInstance("web-sockets", null, $id);
        $this->assertEquals("ID", $session->get("using"));
    }


    public function testRegenerate()
    {
        $this->request("set.php?key=under&value=oath");
        $this->assertRequest("getall.php", ["under" => "oath"]);
        $sessionId = $this->getCookie()->getValue();

        # Ensure the session data is still available, and the session ID has been changed
        $this->request("regenerate.php");
        $this->assertRequest("getall.php", ["under" => "oath"]);
        $this->assertNotSame($sessionId, $this->getCookie()->getValue());
    }


    public function testRefreshCookie()
    {
        $this->request("cookies.php?lifetime=4");

        /**
         * Ensure that when we use the session again 10 seconds later,
         * the expiry time on the cookie is extended, and doesn't
         * still end 15 seconds after the session started.
         */
        sleep(2);
        $time = time();
        $this->request("cookies.php?lifetime=4");

        $cookie = $this->getCookie();

        # We can't test precisely due to timing issues, but check that it's within one second
        $this->assertGreaterThan($time + 3, $cookie->getExpires());
        $this->assertLessThan($time + 5, $cookie->getExpires());
    }
}
