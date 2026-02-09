<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session\Drivers;

use Illuminate\Support\Facades\Cache;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;

class CacheSessionStore implements SessionStore
{
    public function __construct(private ?string $store = null)
    {
    }

    public function get(string $key): ?string
    {
        return Cache::store($this->store)->get($key);
    }

    public function put(string $key, string $value, int $ttl): void
    {
        Cache::store($this->store)->put($key, $value, $ttl);
    }

    public function forget(string $key): void
    {
        Cache::store($this->store)->forget($key);
    }

    public function has(string $key): bool
    {
        return Cache::store($this->store)->has($key);
    }
}
