<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu\Session\Drivers;

use Illuminate\Support\Facades\Storage;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;

class FileSessionStore implements SessionStore
{
    public function __construct(private string $disk = 'local')
    {
    }

    public function get(string $key): ?string
    {
        $path = $this->path($key);

        if (! Storage::disk($this->disk)->exists($path)) {
            return null;
        }

        $data = json_decode(Storage::disk($this->disk)->get($path), true);

        if (! is_array($data) || ! isset($data['value'], $data['expires_at'])) {
            return null;
        }

        if ($data['expires_at'] < time()) {
            $this->forget($key);

            return null;
        }

        return $data['value'];
    }

    public function put(string $key, string $value, int $ttl): void
    {
        $data = json_encode([
            'value' => $value,
            'expires_at' => time() + $ttl,
        ]);

        Storage::disk($this->disk)->put($this->path($key), $data);
    }

    public function forget(string $key): void
    {
        $path = $this->path($key);

        if (Storage::disk($this->disk)->exists($path)) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    private function path(string $key): string
    {
        return "szamlazzhu/sessions/{$key}.json";
    }
}
