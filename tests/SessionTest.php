<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\Exceptions\InvalidNameException;
use duncan3dc\Sessions\Session;
use duncan3dc\Sessions\SessionInstance;
use duncan3dc\Sessions\SessionInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

use function session_name;
use function substr;

class SessionTest extends TestCase
{
    /** @var SessionInterface&MockInterface */
    private $session;


    public function setUp(): void
    {
        $this->session = Mockery::mock(SessionInterface::class);

        # Don't use the mocked instance when we're testing getInstance()
        if (substr((string) $this->getName(), 0, 15) === "testGetInstance") {
            return;
        }

        Session::setInstance($this->session);
    }


    public function tearDown(): void
    {
        Mockery::close();
    }


    public function testSetInstance(): void
    {
        $session = Mockery::mock(SessionInstance::class);
        Session::setInstance($session);
        $this->assertSame($session, Session::getInstance());
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetInstance1(): void
    {
        Session::name("specific-name-234rf387h");
        $session = Session::getInstance();

        # Ensure we get a session instance
        $this->assertInstanceOf(SessionInterface::class, $session);
        $this->assertInstanceOf(SessionInstance::class, $session);

        # Ensure we get the same instance on subsequent calls
        $this->assertSame($session, Session::getInstance());

        # Ensure the session name has been used
        $session->getId();
        $this->assertSame("specific-name-234rf387h", session_name());
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetInstance2(): void
    {
        $this->expectException(InvalidNameException::class);
        $this->expectExceptionMessage("Cannot start session, no name has been specified, you must call Session::name() before using this class");
        Session::getInstance();
    }


    public function testSetInt(): void
    {
        $this->session->shouldReceive("set")->once()->with("one", 1);
        $result = Session::set("one", 1);
        $this->assertNull($result);
    }


    public function testSetString(): void
    {
        $this->session->shouldReceive("set")->once()->with("one", "1");
        $result = Session::set("one", "1");
        $this->assertNull($result);
    }


    public function testSetFloat(): void
    {
        $this->session->shouldReceive("set")->once()->with("one", 1.0);
        $result = Session::set("one", 1.0);
        $this->assertNull($result);
    }


    public function testGetInt(): void
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn(1);
        $result = Session::get("one");
        $this->assertSame(1, $result);
    }


    public function testGetString(): void
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn("1");
        $result = Session::get("one");
        $this->assertSame("1", $result);
    }


    public function testGetFloat(): void
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn(1.0);
        $result = Session::get("one");
        $this->assertSame(1.0, $result);
    }


    public function testGetAll(): void
    {
        $this->session->shouldReceive("getAll")->once()->with()->andReturn([]);
        $result = Session::getAll();
        $this->assertSame([], $result);
    }


    public function testUnset(): void
    {
        $this->session->shouldReceive("delete")->once()->with("one");
        $result = Session::delete("one");
        $this->assertNull($result);
    }


    public function testUnsetArray(): void
    {
        $this->session->shouldReceive("delete")->once()->with("one", "three");
        $result = Session::delete("one", "three");
        $this->assertNull($result);
    }


    public function testClear(): void
    {
        $this->session->shouldReceive("clear")->once()->with();
        $result = Session::clear();
        $this->assertNull($result);
    }


    public function testDestroy(): void
    {
        $session = Mockery::mock(SessionInstance::class);
        Session::setInstance($session);

        $session->shouldReceive("destroy")->once()->with();
        $result = Session::destroy();
        $this->assertNull($result);
    }


    public function testGetSet1(): void
    {
        $this->session->shouldReceive("getSet")->once()->with("field", "default", false)->andReturn("ok");
        $result = Session::getSet("field", "default");
        $this->assertSame("ok", $result);
    }
    public function testGetSet2(): void
    {
        $this->session->shouldReceive("getSet")->once()->with("field", null, false)->andReturn(7.0);
        $result = Session::getSet("field");
        $this->assertSame(7.0, $result);
    }
    public function testGetSet3(): void
    {
        $this->session->shouldReceive("getSet")->once()->with("field", "default", true)->andReturn("3");
        $result = Session::getSet("field", "default", true);
        $this->assertSame("3", $result);
    }


    public function testCreateNamespace(): void
    {
        $namespace = Mockery::mock(SessionInterface::class);
        $this->session->shouldReceive("createNamespace")->once()->with("extra")->andReturn($namespace);
        $result = Session::createNamespace("extra");
        $this->assertSame($namespace, $result);
    }


    public function testSetFlash(): void
    {
        $this->session->shouldReceive("setFlash")->once()->with("field", "boom!");
        $result = Session::setFlash("field", "boom!");
        $this->assertNull($result);
    }


    public function testGetFlash(): void
    {
        $this->session->shouldReceive("getFlash")->once()->with("field")->andReturn("boom!");
        $result = Session::getFlash("field");
        $this->assertSame("boom!", $result);
    }
}
