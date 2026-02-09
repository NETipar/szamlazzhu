<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session\Contracts;

interface SessionStore
{
    public function get(string $key): ?string;

    public function put(string $key, string $value, int $ttl): void;

    public function forget(string $key): void;

    public function has(string $key): bool;
}
