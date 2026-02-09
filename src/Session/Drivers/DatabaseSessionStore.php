<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session\Drivers;

use Illuminate\Support\Facades\DB;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;

class DatabaseSessionStore implements SessionStore
{
    public function __construct(private string $table = 'szamlazzhu_sessions')
    {
    }

    public function get(string $key): ?string
    {
        $record = DB::table($this->table)
            ->where('key', $key)
            ->where('expires_at', '>', now())
            ->first();

        if ($record === null) {
            return null;
        }

        return $record->value;
    }

    public function put(string $key, string $value, int $ttl): void
    {
        DB::table($this->table)->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'expires_at' => now()->addSeconds($ttl),
            ]
        );
    }

    public function forget(string $key): void
    {
        DB::table($this->table)->where('key', $key)->delete();
    }

    public function has(string $key): bool
    {
        return DB::table($this->table)
            ->where('key', $key)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
