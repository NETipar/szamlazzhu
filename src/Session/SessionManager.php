<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session;

use InvalidArgumentException;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\Drivers\CacheSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\DatabaseSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\FileSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\NullSessionStore;

class SessionManager
{
    private SessionStore $store;

    private string $prefix;

    private int $ttl;

    /**
     * @param array{
     *     session: array{
     *         driver: string,
     *         ttl?: int,
     *         prefix?: string,
     *         store?: string,
     *         disk?: string,
     *         table?: string
     *     }
     * } $config
     */
    public function __construct(array $config)
    {
        $sessionConfig = $config['session'] ?? [];

        $this->prefix = $sessionConfig['prefix'] ?? 'szamlazzhu_session_';
        $this->ttl = $sessionConfig['ttl'] ?? 3600;
        $this->store = $this->createDriver($sessionConfig);
    }

    public function getSessionId(string $apiKeyHash): ?string
    {
        return $this->store->get($this->buildKey($apiKeyHash));
    }

    public function setSessionId(string $apiKeyHash, string $sessionId): void
    {
        $this->store->put($this->buildKey($apiKeyHash), $sessionId, $this->ttl);
    }

    public function forgetSessionId(string $apiKeyHash): void
    {
        $this->store->forget($this->buildKey($apiKeyHash));
    }

    public function getStore(): SessionStore
    {
        return $this->store;
    }

    private function buildKey(string $apiKeyHash): string
    {
        return $this->prefix.$apiKeyHash;
    }

    private function createDriver(array $config): SessionStore
    {
        $driver = $config['driver'] ?? 'cache';

        return match ($driver) {
            'cache' => new CacheSessionStore($config['store'] ?? null),
            'file' => new FileSessionStore($config['disk'] ?? 'local'),
            'database' => new DatabaseSessionStore($config['table'] ?? 'szamlazzhu_sessions'),
            'null' => new NullSessionStore,
            default => throw new InvalidArgumentException("Unsupported session driver: {$driver}"),
        };
    }
}
