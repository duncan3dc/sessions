<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\SessionInterface;
use duncan3dc\Sessions\SessionNamespace;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class SessionNamespaceTest extends TestCase
{
    private SessionNamespace $namespace;

    /** @var SessionInterface&MockInterface */
    private SessionInterface $session;


    public function setUp(): void
    {
        $this->session = Mockery::mock(SessionInterface::class);
        $this->namespace = new SessionNamespace("test", $this->session);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }


    public function testSetInt(): void
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", 1);
        $result = $this->namespace->set("one", 1);
        $this->assertSame($this->namespace, $result);
    }


    public function testSetString(): void
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", "1");
        $result = $this->namespace->set("one", "1");
        $this->assertSame($this->namespace, $result);
    }


    public function testSetFloat(): void
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test_one", 1.0);
        $result = $this->namespace->set("one", 1.0);
        $this->assertSame($this->namespace, $result);
    }


    public function testGetInt(): void
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn(1);
        $result = $this->namespace->get("one");
        $this->assertSame(1, $result);
    }


    public function testGetString(): void
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn("1");
        $result = $this->namespace->get("one");
        $this->assertSame("1", $result);
    }


    public function testGetFloat(): void
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test_one")->andReturn(1.0);
        $result = $this->namespace->get("one");
        $this->assertSame(1.0, $result);
    }


    public function testUnset(): void
    {
        $this->session->shouldReceive("set")->once()->with(["_ns_test_one" => null], null);
        $result = $this->namespace->delete("one");
        $this->assertSame($this->namespace, $result);
    }


    public function testUnsetArray(): void
    {
        $this->session->shouldReceive("set")->once()->with(["_ns_test_one" => null, "_ns_test_three" => null], null);
        $result = $this->namespace->delete("one", "three");
        $this->assertSame($this->namespace, $result);
    }


    public function testClear(): void
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


    public function testCreateNamespace(): void
    {
        $extra = $this->namespace->createNamespace("extra");

        $this->session->shouldReceive("set")->once()->with("_ns__ns_test_extra_one", 1);
        $result = $extra->set("one", 1);

        $this->assertSame($extra, $result);
    }


    public function testSetFlash(): void
    {
        $this->session->shouldReceive("set")->once()->with("_ns_test__fs_field", "boom!");
        $result = $this->namespace->setFlash("field", "boom!");
        $this->assertSame($this->namespace, $result);
    }


    public function testGetFlash(): void
    {
        $this->session->shouldReceive("get")->once()->with("_ns_test__fs_field")->andReturn("boom!");
        $this->session->shouldReceive("set")->once()->with(["_ns_test__fs_field" => null], null);

        $result = $this->namespace->getFlash("field");
        $this->assertSame("boom!", $result);
    }
}
