<?php

namespace Losgif\YS7Laravel;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

/**
 * Class YS7ServiceProvider
 *
 * @package Losgif\YS7Laravel
 */
class YS7ServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Abstract type to bind YS7 Client as in the Service Container.
     *
     * @var string
     */
    public static $abstract = 'ys7';

    /**
     * Setup the configuration's file.
     */
    public function setupConfig()
    {
        $this->publishes([
            __DIR__.'/config/ys7.php' => config_path('ys7.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/ys7.php',
            'ys7'
        );
    }

    /**
     * 注册服务提供者
     */
    public function register(): void
    {
        $this->setupConfig();

        $this->app->singleton(YS7Auth::class, function () {
            $appKey = config('ys7.app_key');
            $appSecret = config('ys7.app_secret');

            return new YS7Auth($appKey, $appSecret);
        });

        $this->app->alias(YS7Auth::class, static::$abstract . '.auth');

        $this->app->singleton(YS7Client::class, function () {
            $auth = app('ys7.auth');

            return new YS7Client($auth);
        });

        $this->app->alias(YS7Client::class, static::$abstract);
    }

    /**
     * 服务提供者配置
     *
     * @return array|string[]
     */
    public function provides(): array
    {
        return [
            static::$abstract
        ];
    }
}
