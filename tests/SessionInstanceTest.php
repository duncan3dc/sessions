<?php

namespace duncan3dc\SessionsTest;

use duncan3dc\Sessions\SessionInstance;
use PHPUnit\Framework\TestCase;
use function session_set_save_handler;

class SessionInstanceTest extends TestCase
{
    /**
     * @var SessionInstance $session The instance we are testing.
     */
    private $session;


    public function setUp()
    {
        session_set_save_handler(new SessionHandler);
        $this->session = new SessionInstance("test");
    }


    public function testConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot start session, no name has been specified");
        new SessionInstance("");
    }


    public function testGetAll()
    {
        $this->session->set("one", 1);
        $this->session->set([
            "two"   =>  2,
            "three" =>  3,
        ]);

        $this->assertSame([
            "one"   =>  1,
            "two"   =>  2,
            "three" =>  3,
        ], $this->session->getAll());
    }


    public function testInt()
    {
        $result = $this->session->set("one", 1);
        $this->assertSame($this->session, $result);
        $this->assertSame(1, $this->session->get("one"));
    }


    public function testString()
    {
        $result = $this->session->set("one", "1");
        $this->assertSame($this->session, $result);
        $this->assertSame("1", $this->session->get("one"));
    }


    public function testFloat()
    {
        $result = $this->session->set("one", 1.0);
        $this->assertSame($this->session, $result);
        $this->assertSame(1.0, $this->session->get("one"));
    }


    public function testRegenerate()
    {
        $originalId = session_id();
        $result = $this->session->regenerate();
        $this->assertNotSame($originalId, $result);
    }


    public function testSerialize()
    {
        $obj = new \stdClass;
        $obj->one = 1;
        $obj->two = 2;

        $this->session->set("one", $obj);
        $this->assertSame($obj, $this->session->get("one"));
    }


    public function testSetTwice()
    {
        $result = $this->session->set("one", "ok");
        $this->assertSame($this->session, $result);
        $result = $this->session->set("one", "ok");
        $this->assertSame($this->session, $result);
    }


    public function testGetSet1()
    {
        $_POST["field1"] = "post";
        $_GET["field1"] = "get";
        $this->session->set("field1", "existing");

        $this->assertSame("post", $this->session->getSet("field1"));
    }
    public function testGetSet2()
    {
        $_POST["field2"] = "post";
        $_GET["field2"] = "get";
        $this->session->set("field2", "existing");

        $this->assertSame("post", $this->session->getSet("field2", "default", true));
    }
    public function testGetSet3()
    {
        $_GET["field3"] = "get";
        $this->session->set("fiel3", "existing");

        $this->assertSame("get", $this->session->getSet("field3"));
    }
    public function testGetSet4()
    {
        $_GET["field4"] = "get";
        $this->session->set("field4", "existing");

        $this->assertSame("get", $this->session->getSet("field4", "default", true));
    }
    public function testGetSet5()
    {
        $this->session->set("field5", "existing");

        $this->assertSame("existing", $this->session->getSet("field5"));
    }
    public function testGetSet6()
    {
        $this->session->set("field6", "existing");

        $this->assertSame("existing", $this->session->getSet("field6", "default", true));
    }
    public function testGetSet7()
    {
        $this->assertSame(null, $this->session->getSet("field7"));
    }
    public function testGetSet8()
    {
        $this->assertSame("default", $this->session->getSet("field8", "default", true));
    }


    public function testUnset()
    {
        $this->session->set("one", 1);
        $this->assertSame(1, $this->session->get("one"));
        $this->session->delete("one");
        $this->assertNull($this->session->get("one"));
    }


    public function testUnsetArray()
    {
        $this->session->set([
            "one"   =>  1,
            "two"   =>  2,
            "three" =>  3,
        ]);
        $this->assertSame(1, $this->session->get("one"));
        $this->assertSame(2, $this->session->get("two"));
        $this->assertSame(3, $this->session->get("three"));

        $this->session->delete("one", "three");

        $this->assertNull($this->session->get("one"));
        $this->assertSame(2, $this->session->get("two"));
        $this->assertNull($this->session->get("three"));
    }


    public function testClear()
    {
        $this->session->set([
            "one"   =>  1,
            "two"   =>  2,
            "three" =>  3,
        ]);
        $this->assertSame(1, $this->session->get("one"));
        $this->assertSame(2, $this->session->get("two"));
        $this->assertSame(3, $this->session->get("three"));

        $this->session->clear();

        $this->assertNull($this->session->get("one"));
        $this->assertNull($this->session->get("two"));
        $this->assertNull($this->session->get("three"));
    }


    public function testDestroy()
    {
        $this->session->set("album", "kezia");
        $this->assertSame("kezia", $this->session->get("album"));

        $this->session->destroy();

        $this->assertSame(null, $this->session->get("album"));
    }


    public function testCreateNamespace()
    {
        $extra = $this->session->createNamespace("extra");

        $result = $extra->set("one", 1);

        $this->assertSame($extra, $result);
        $this->assertNull($this->session->get("one"));
        $this->assertSame(1, $extra->get("one"));
    }
    public function testCreateNamespaceClash()
    {
        $one = $this->session->createNamespace("one");
        $two = $one->createNamespace("two");

        $one->set("two", 2);

        $result = $two->set("three", 3);

        # Ensure we can chain and our sub-namespace stored a value
        $this->assertSame($two, $result);
        $this->assertSame(3, $two->get("three"));

        # Ensure none of our namespaced keys poluted the global space
        $this->assertNull($this->session->get("one"));
        $this->assertNull($this->session->get("two"));

        # Ensure our first namespace was able to store a value with the same key as the sub-namespace
        $this->assertSame(2, $one->get("two"));
    }


    public function testFlash()
    {
        $this->session->setFlash("field", "boom!");
        $this->assertSame("boom!", $this->session->getFlash("field"));
        $this->assertSame(null, $this->session->getFlash("field"));
    }
    public function testFlashClash()
    {
        $this->session->setFlash("field", "boom!");
        $this->session->set("field", "value");

        $this->assertSame("boom!", $this->session->getFlash("field"));
        $this->assertSame("value", $this->session->get("field"));
    }
}
