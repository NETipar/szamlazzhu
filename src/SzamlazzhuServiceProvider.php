<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu;

use Illuminate\Support\ServiceProvider;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\Drivers\CacheSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\DatabaseSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\FileSessionStore;
use NETipar\Szamlazzhu\Session\Drivers\NullSessionStore;
use NETipar\Szamlazzhu\Session\SessionManager;

class SzamlazzhuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/szamlazzhu.php', 'szamlazzhu');

        $this->app->singleton(SessionStore::class, function ($app) {
            $config = $app['config']['szamlazzhu'];
            $driver = $config['session']['driver'] ?? 'cache';

            return match ($driver) {
                'cache' => new CacheSessionStore($config['session']['cache_store'] ?? null),
                'file' => new FileSessionStore($config['storage']['disk'] ?? 'local'),
                'database' => new DatabaseSessionStore,
                'null' => new NullSessionStore,
                default => new CacheSessionStore,
            };
        });

        $this->app->singleton(SessionManager::class, function ($app) {
            return new SessionManager($app['config']['szamlazzhu']);
        });

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $app['config']['szamlazzhu'],
                $app->make(SessionManager::class),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/szamlazzhu.php' => config_path('szamlazzhu.php'),
            ], 'szamlazzhu-config');
        }
    }
}
