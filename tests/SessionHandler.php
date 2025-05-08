<?php

namespace duncan3dc\SessionsTest;

class SessionHandler implements \SessionHandlerInterface
{
    private string $data = "";


    public function close(): bool
    {
        return true;
    }


    public function destroy(string $id): bool
    {
        $this->data = "";
        return true;
    }


    public function gc(int $max): int|false
    {
        return 0;
    }


    public function open(string $path, string $name): bool
    {
        return true;
    }


    public function read(string $id): string|false
    {
        return $this->data;
    }


    public function write(string $id, string $data): bool
    {
        $this->data = $data;
        return true;
    }
}
