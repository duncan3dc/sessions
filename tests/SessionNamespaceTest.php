<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\SessionNamespace;
use duncan3dc\Sessions\SessionInstance;
use Mockery;
use PHPUnit\Framework\TestCase;

class SessionNamespaceTest extends TestCase
{
    protected $session;
    protected $namespace;

    public function setUp()
    {
        $this->session = Mockery::mock(SessionInstance::class);
        $this->namespace = new SessionNamespace("test", $this->session);
    }

    public function tearDown()
    {
        Mockery::close();
    }


    public function testSetInt()
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", 1);
        $result = $this->namespace->set("one", 1);
        $this->assertSame($this->namespace, $result);
    }


    public function testSetString()
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", "1");
        $result = $this->namespace->set("one", "1");
        $this->assertSame($this->namespace, $result);
    }


    public function testSetFloat()
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", 1.0);
        $result = $this->namespace->set("one", 1.0);
        $this->assertSame($this->namespace, $result);
    }


    public function testGetInt()
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn(1);
        $result = $this->namespace->get("one");
        $this->assertSame(1, $result);
    }


    public function testGetString()
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn("1");
        $result = $this->namespace->get("one");
        $this->assertSame("1", $result);
    }


    public function testGetFloat()
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn(1.0);
        $result = $this->namespace->get("one");
        $this->assertSame(1.0, $result);
    }


    public function testUnset()
    {
        $this->session->shouldReceive("set")->once()->with(["_ns_test_one" => null], null);
        $result = $this->namespace->delete("one");
        $this->assertSame($this->namespace, $result);
    }


    public function testUnsetArray()
    {
        $this->session->shouldReceive("set")->once()->with(["_ns_test_one" => null, "_ns_test_three" => null], null);
        $result = $this->namespace->delete("one", "three");
        $this->assertSame($this->namespace, $result);
    }


    public function testClear()
    {
        $this->session->shouldReceive("getAll")->once()->with()->andReturn([
            "_ns_test_one"  =>  "one",
            "not_for_you"   =>  "huh",
            "_ns_test_two"  =>  "two",
        ]);

        $this->session->shouldReceive("set")->once()->with([
            "_ns_test_one"  =>  null,
            "_ns_test_two"  =>  null,
        ], null);
        $result = $this->namespace->clear();

        $this->assertSame($this->namespace, $result);
    }


    public function testCreateNamespace()
    {
        $extra = $this->namespace->createNamespace("extra");

        $this->session->shouldReceive("set")->once()->with("_ns__ns_test_extra_one", 1);
        $result = $extra->set("one", 1);

        $this->assertSame($extra, $result);
    }


    public function testSetFlash()
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test__fs_field", "boom!");
        $result = $this->namespace->setFlash("field", "boom!");
        $this->assertSame($this->namespace, $result);
    }


    public function testGetFlash()
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test__fs_field")->andReturn("boom!");
        $this->session->shouldReceive("set")->once()->with(["_ns_test__fs_field" => null], null);

        $result = $this->namespace->getFlash("field");
        $this->assertSame("boom!", $result);
    }
}
