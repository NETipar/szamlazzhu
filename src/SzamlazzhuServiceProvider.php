<?php

declare(strict_types=1);

namespace NETipar\Szamlazzhu;

use Illuminate\Support\ServiceProvider;
use NETipar\Szamlazzhu\Http\ApiRequestFactory;
use NETipar\Szamlazzhu\Session\Contracts\SessionStore;
use NETipar\Szamlazzhu\Session\SessionManager;

class SzamlazzhuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/szamlazzhu.php', 'szamlazzhu');

        $this->app->singleton(SessionManager::class, function ($app) {
            return new SessionManager($app['config']['szamlazzhu']);
        });

        $this->app->singleton(SessionStore::class, function ($app) {
            return $app->make(SessionManager::class)->getStore();
        });

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $app['config']['szamlazzhu'],
                $app->make(SessionManager::class),
                $app->make(ApiRequestFactory::class),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/szamlazzhu.php' => config_path('szamlazzhu.php'),
            ], 'szamlazzhu-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_szamlazzhu_sessions_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_szamlazzhu_sessions_table.php'),
            ], 'szamlazzhu-migrations');
        }
    }
}
