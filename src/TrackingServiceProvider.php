<?php

namespace Feeldee\Tracking;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class TrackingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/tracking.php',
            'tracking'
        );
    }

    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [];

    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 追加設定
        $this->publishes([
            __DIR__ . '/../config/tracking.php' => config_path('tracking.php'),
        ]);

        // 追加マイグレーション
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // 追加情報
        AboutCommand::add('Feeldee', fn() => ['Tracking Version' => '1.0.0']);

        // 追加ミドルウェア
        $router = $this->app['router'];
        $router->aliasMiddleware('tracking', \Feeldee\Tracking\Http\Middleware\TrackingRequests::class);
    }
}
