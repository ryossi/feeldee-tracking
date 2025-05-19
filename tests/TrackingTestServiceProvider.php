<?php

namespace Tests;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Support\ServiceProvider;

class TrackingTestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom('tests/routes/test.php');

        $router = $this->app['router'];
        $router->aliasMiddleware('queued.cookies', AddQueuedCookiesToResponse::class);
    }
}
