<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use NETipar\Szamlazzhu\Http\ApiRequestFactory;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\SessionManager;

class SzamlazzhuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/szamlazzhu.php', 'szamlazzhu');

        $this->app->singleton(SessionManager::class, function (Application $app): SessionManager {
            return new SessionManager($app->make('config')->get('szamlazzhu'));
        });

        $this->app->singleton(SessionStore::class, function (Application $app): SessionStore {
            return $app->make(SessionManager::class)->getStore();
        });

        $this->app->singleton(Client::class, function (Application $app): Client {
            return new Client(
                $app->make('config')->get('szamlazzhu'),
                $app->make(SessionManager::class),
                $app->make(ApiRequestFactory::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/szamlazzhu.php' => config_path('szamlazzhu.php'),
        ], ['szamlazzhu', 'szamlazzhu-config']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['szamlazzhu', 'szamlazzhu-migrations']);
    }
}
