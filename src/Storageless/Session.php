<?php

namespace duncan3dc\Sessions\Storageless;

use duncan3dc\Sessions\SessionInterface;
use duncan3dc\Sessions\SessionNamespace;
use duncan3dc\Sessions\SessionTrait;
use PSR7Sessions\Storageless\Session\SessionInterface as StoragelessInterface;

use function is_array;

final class Session implements SessionInterface
{
    use SessionTrait;

    /**
     * @var StoragelessInterface $session The instance we are wrapping.
     */
    private $session;


    public function __construct(StoragelessInterface $session)
    {
        $this->session = $session;
    }


    public function createNamespace(string $name): SessionInterface
    {
        return new SessionNamespace($name, $this);
    }


    public function get(string $key)
    {
        return $this->session->get($key);
    }


    public function getAll(): array
    {
        return (array) $this->session->jsonSerialize();
    }


    public function set($data, $value = null): SessionInterface
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $this->session->set($key, $val);
            }
        } else {
            $this->session->set($data, $value);
        }

        return $this;
    }


    /**
     * Unset a value within session data.
     *
     * @param string ...$keys All the keys to remove from the session
     *
     * @return SessionInterface
     */
    public function delete(string ...$keys): SessionInterface
    {
        foreach ($keys as $key) {
            $this->session->remove($key);
        }

        return $this;
    }


    public function clear(): SessionInterface
    {
        $this->session->clear();

        return $this;
    }


    public function destroy(): void
    {
        $this->session->clear();
    }
}
