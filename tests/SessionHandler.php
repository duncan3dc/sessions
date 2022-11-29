<?php

namespace duncan3dc\SessionsTest;

class SessionHandler implements \SessionHandlerInterface
{
    /** @var string */
    private $data = "";

    public function close()
    {
        return true;
    }


    public function destroy($id)
    {
        $this->data = "";
        return true;
    }


    public function gc($max)
    {
        return true;
    }


    public function open($path, $name)
    {
        return true;
    }


    public function read($id)
    {
        return $this->data;
    }


    public function write($id, $data)
    {
        $this->data = $data;
        return true;
    }
}
