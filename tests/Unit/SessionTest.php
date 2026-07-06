<?php

use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\Drivers\CacheSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\DatabaseSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\NullSessionStore;
use NETipar\Szamlazzhu\Session\SessionManager;

function inMemorySessionStore(): SessionStore
{
    return new class implements SessionStore
    {
        private array $data = [];

        public function get(string $key): ?string
        {
            return $this->data[$key] ?? null;
        }

        public function put(string $key, string $value, int $ttl): void
        {
            $this->data[$key] = $value;
        }

        public function forget(string $key): void
        {
            unset($this->data[$key]);
        }

        public function has(string $key): bool
        {
            return isset($this->data[$key]);
        }
    };
}

it('uses null driver in session manager', function () {
    $manager = new SessionManager([
        'session' => [
            'driver' => 'null',
            'ttl' => 3600,
        ],
    ]);

    expect($manager->getStore())->toBeInstanceOf(NullSessionStore::class);
});

it('stores and retrieves session id', function () {
    $store = new class implements SessionStore
    {
        private array $data = [];

        public function get(string $key): ?string
        {
            return $this->data[$key] ?? null;
        }

        public function put(string $key, string $value, int $ttl): void
        {
            $this->data[$key] = $value;
        }

        public function forget(string $key): void
        {
            unset($this->data[$key]);
        }

        public function has(string $key): bool
        {
            return isset($this->data[$key]);
        }
    };

    $manager = new SessionManager([
        'session' => [
            'driver' => 'null',
            'prefix' => 'test_',
            'ttl' => 3600,
        ],
    ]);

    // Use reflection to replace the store
    $ref = new ReflectionProperty($manager, 'store');
    $ref->setValue($manager, $store);

    $apiKeyHash = md5('test-key');

    expect($manager->getSessionId($apiKeyHash))->toBeNull();

    $manager->setSessionId($apiKeyHash, 'session-123');

    expect($manager->getSessionId($apiKeyHash))->toBe('session-123');

    $manager->forgetSessionId($apiKeyHash);

    expect($manager->getSessionId($apiKeyHash))->toBeNull();
});

it('null session store always returns null', function () {
    $store = new NullSessionStore;

    expect($store->get('anything'))->toBeNull()
        ->and($store->has('anything'))->toBeFalse();

    $store->put('key', 'value', 3600);

    expect($store->get('key'))->toBeNull();
});

it('throws for unsupported session driver', function () {
    new SessionManager([
        'session' => [
            'driver' => 'unsupported',
        ],
    ]);
})->throws(InvalidArgumentException::class, 'Unsupported session driver: unsupported');

it('honors cache_prefix from config', function () {
    $store = inMemorySessionStore();

    $manager = new SessionManager([
        'session' => [
            'driver' => 'null',
            'cache_prefix' => 'configured_',
            'ttl' => 3600,
        ],
    ]);

    $ref = new ReflectionProperty($manager, 'store');
    $ref->setValue($manager, $store);

    $manager->setSessionId('abc123', 'session-val');

    expect($store->get('configured_abc123'))->toBe('session-val');
});

it('falls back to legacy prefix key', function () {
    $store = inMemorySessionStore();

    $manager = new SessionManager([
        'session' => [
            'driver' => 'null',
            'prefix' => 'legacy_',
            'ttl' => 3600,
        ],
    ]);

    $ref = new ReflectionProperty($manager, 'store');
    $ref->setValue($manager, $store);

    $manager->setSessionId('abc123', 'session-val');

    expect($store->get('legacy_abc123'))->toBe('session-val');
});

it('creates cache driver with cache_store from config', function () {
    $manager = new SessionManager([
        'session' => [
            'driver' => 'cache',
            'cache_store' => 'array',
        ],
    ]);

    $store = $manager->getStore();

    expect($store)->toBeInstanceOf(CacheSessionStore::class);

    $ref = new ReflectionProperty($store, 'store');

    expect($ref->getValue($store))->toBe('array');
});

it('creates database driver with configured table', function () {
    $manager = new SessionManager([
        'session' => [
            'driver' => 'database',
            'table' => 'custom_sessions',
        ],
    ]);

    $store = $manager->getStore();

    expect($store)->toBeInstanceOf(DatabaseSessionStore::class);

    $ref = new ReflectionProperty($store, 'table');

    expect($ref->getValue($store))->toBe('custom_sessions');
});

it('builds session key with prefix', function () {
    $manager = new SessionManager([
        'session' => [
            'driver' => 'null',
            'prefix' => 'custom_prefix_',
            'ttl' => 3600,
        ],
    ]);

    // The null store won't persist, but we can verify the manager builds keys
    $manager->setSessionId('abc123', 'session-val');

    // Since NullSessionStore doesn't persist, we just verify no exception thrown
    expect($manager->getSessionId('abc123'))->toBeNull();
});
