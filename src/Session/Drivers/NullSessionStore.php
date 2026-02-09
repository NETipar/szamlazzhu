<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session\Drivers;

use NETipar\Szamlazzhu\Session\Contracts\SessionStore;

class NullSessionStore implements SessionStore
{
    public function get(string $key): ?string
    {
        return null;
    }

    public function put(string $key, string $value, int $ttl): void
    {
    }

    public function forget(string $key): void
    {
    }

    public function has(string $key): bool
    {
        return false;
    }
}
