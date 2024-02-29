<?php

namespace duncan3dc\SessionsTest;

class SessionHandler implements \SessionHandlerInterface
{
    /** @var string */
    private $data = "";

    #[\ReturnTypeWillChange]
    public function close()
    {
        return true;
    }


    #[\ReturnTypeWillChange]
    public function destroy($id)
    {
        $this->data = "";
        return true;
    }


    #[\ReturnTypeWillChange]
    public function gc($max)
    {
        return true;
    }


    #[\ReturnTypeWillChange]
    public function open($path, $name)
    {
        return true;
    }


    #[\ReturnTypeWillChange]
    public function read($id)
    {
        return $this->data;
    }


    #[\ReturnTypeWillChange]
    public function write($id, $data)
    {
        $this->data = $data;
        return true;
    }
}
