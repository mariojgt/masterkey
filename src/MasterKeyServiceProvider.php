<?php

namespace Mariojgt\MasterKey;

use Illuminate\Support\ServiceProvider;

class MasterKeyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/masterkey.php', 'masterkey');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/masterkey.php' => config_path('masterkey.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'masterkey');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
