<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\Session;
use duncan3dc\Sessions\SessionInstance;
use Mockery;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    protected $session;

    public function setUp()
    {
        Session::name("test");

        $this->session = Mockery::mock(SessionInstance::class);

        $reflection = new \ReflectionClass(Session::class);
        $session = $reflection->getProperty("session");
        $session->setAccessible(true);
        $session->setValue($this->session);
    }

    public function tearDown()
    {
        Mockery::close();
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
        Session::set("one", "1");
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
        $this->session->shouldReceive("createNamespace")->once()->with("extra")->andReturn("ok");
        $result = Session::createNamespace("extra");
        $this->assertSame("ok", $result);
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
