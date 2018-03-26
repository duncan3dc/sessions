<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\Session;
use duncan3dc\Sessions\SessionInstance;
use duncan3dc\Sessions\SessionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use function session_name;
use function substr;

class SessionTest extends TestCase
{
    /**
     * @var Mockery $session A SessionInstance to use for testing.
     */
    private $session;


    public function setUp()
    {
        $this->session = Mockery::mock(SessionInterface::class);

        # Don't use the mocked instance when we're testing getInstance()
        if (substr($this->getName(), 0, 15) === "testGetInstance") {
            return;
        }

        Session::setInstance($this->session);
    }


    public function tearDown()
    {
        Mockery::close();
    }


    public function testSetInstance()
    {
        $session = Mockery::mock(SessionInstance::class);
        Session::setInstance($session);
        $this->assertSame($session, Session::getInstance());
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testGetInstance1()
    {
        Session::name("specific-name-234rf387h");
        $session = Session::getInstance();

        # Ensure we get a session instance
        $this->assertInstanceOf(SessionInterface::class, $session);

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
    public function testGetInstance2()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot start session, no name has been specified, you must call Session::name() before using this class");
        Session::getInstance();
    }


    public function testSetInt()
    {
        $this->session->shouldReceive("set")->once()->with("one", 1);
        $result = Session::set("one", 1);
        $this->assertNull($result);
    }


    public function testSetString()
    {
        $this->session->shouldReceive("set")->once()->with("one", "1");
        $result = Session::set("one", "1");
        $this->assertNull($result);
    }


    public function testSetFloat()
    {
        $this->session->shouldReceive("set")->once()->with("one", 1.0);
        $result = Session::set("one", 1.0);
        $this->assertNull($result);
    }


    public function testGetInt()
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn(1);
        $result = Session::get("one");
        $this->assertSame(1, $result);
    }


    public function testGetString()
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn("1");
        $result = Session::get("one");
        $this->assertSame("1", $result);
    }


    public function testGetFloat()
    {
        $this->session->shouldReceive("get")->once()->with("one")->andReturn(1.0);
        $result = Session::get("one");
        $this->assertSame(1.0, $result);
    }


    public function testGetAll()
    {
        $this->session->shouldReceive("getAll")->once()->with()->andReturn([]);
        $result = Session::getAll();
        $this->assertSame([], $result);
    }


    public function testUnset()
    {
        $this->session->shouldReceive("delete")->once()->with("one");
        $result = Session::delete("one");
        $this->assertNull($result);
    }


    public function testUnsetArray()
    {
        $this->session->shouldReceive("delete")->once()->with("one", "three");
        $result = Session::delete("one", "three");
        $this->assertNull($result);
    }


    public function testClear()
    {
        $this->session->shouldReceive("clear")->once()->with();
        $result = Session::clear();
        $this->assertNull($result);
    }


    public function testDestroy()
    {
        $this->session->shouldReceive("destroy")->once()->with();
        $result = Session::destroy();
        $this->assertNull($result);
    }


    public function testGetSet1()
    {
        $this->session->shouldReceive("getSet")->once()->with("field", "default", false)->andReturn("ok");
        $result = Session::getSet("field", "default");
        $this->assertSame("ok", $result);
    }
    public function testGetSet2()
    {
        $this->session->shouldReceive("getSet")->once()->with("field", null, false)->andReturn(7.0);
        $result = Session::getSet("field");
        $this->assertSame(7.0, $result);
    }
    public function testGetSet3()
    {
        $this->session->shouldReceive("getSet")->once()->with("field", "default", true)->andReturn("3");
        $result = Session::getSet("field", "default", true);
        $this->assertSame("3", $result);
    }


    public function testCreateNamespace()
    {
        $namespace = Mockery::mock(SessionInterface::class);
        $this->session->shouldReceive("createNamespace")->once()->with("extra")->andReturn($namespace);
        $result = Session::createNamespace("extra");
        $this->assertSame($namespace, $result);
    }


    public function testSetFlash()
    {
        $this->session->shouldReceive("setFlash")->once()->with("field", "boom!");
        $result = Session::setFlash("field", "boom!");
        $this->assertNull($result);
    }


    public function testGetFlash()
    {
        $this->session->shouldReceive("getFlash")->once()->with("field")->andReturn("boom!");
        $result = Session::getFlash("field");
        $this->assertSame("boom!", $result);
    }
}
