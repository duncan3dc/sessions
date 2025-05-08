<?php

namespace duncan3dc\SessionsTest\Storageless;

use duncan3dc\Sessions\Storageless\Session;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use PSR7Sessions\Storageless\Session\DefaultSessionData;
use PSR7Sessions\Storageless\Session\SessionInterface;

class SessionTest extends TestCase
{
    private Session $session;

    /** @var SessionInterface&MockInterface */
    private SessionInterface $storageless;


    public function setUp(): void
    {
        $this->storageless = Mockery::mock(SessionInterface::class);
        $this->session = new Session($this->storageless);
    }


    public function tearDown(): void
    {
        Mockery::close();
    }


    public function testCreateNamespace(): void
    {
        $this->storageless->shouldReceive("set")->once()->with("_ns_inner_key", "value");

        $inner = $this->session->createNamespace("inner");
        $inner->set("key", "value");

        $this->storageless->shouldReceive("get")->once()->with("outer")->andReturn("yep");
        $this->assertSame("yep", $this->session->get("outer"));
    }


    public function testGet(): void
    {
        $this->storageless->shouldReceive("get")->once()->with("key")->andReturn("value");
        $this->assertSame("value", $this->session->get("key"));
    }


    public function testGetAll(): void
    {
        $storageless = DefaultSessionData::newEmptySession();
        $storageless->set("one", 1);
        $storageless->set("two", 2);

        $session = new Session($storageless);
        $this->assertSame([
            "one" => 1,
            "two" => 2,
        ], $session->getAll());
    }


    public function testSet1(): void
    {
        $this->storageless->shouldReceive("set")->once()->with("key", "value");
        $this->assertSame($this->session, $this->session->set("key", "value"));
    }
    public function testSet2(): void
    {
        $this->storageless->shouldReceive("set")->once()->with("key1", "value1");
        $this->storageless->shouldReceive("set")->once()->with("key2", "value2");
        $result = $this->session->set([
            "key1" => "value1",
            "key2" => "value2",
        ]);
        $this->assertSame($this->session, $result);
    }


    public function testDelete(): void
    {
        $this->storageless->shouldReceive("remove")->once()->with("key1");
        $this->storageless->shouldReceive("remove")->once()->with("key2");
        $this->assertSame($this->session, $this->session->delete("key1", "key2"));
    }


    public function testClear(): void
    {
        $this->storageless->shouldReceive("clear")->once()->with();
        $this->assertSame($this->session, $this->session->clear());
    }
}
