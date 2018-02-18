<?php

namespace Semok\Support\Middleware\ResponseCache;

use Config;
use Illuminate\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->bind(CacheProfiles\CacheProfile::class, function (Application $app) {
            return $app->make(config('semok.middleware.responsecache.cache_profile'));
        });

        $this->app->when(ResponseCacheRepository::class)
            ->needs(Repository::class)
            ->give(function (): Repository {
                return $this->app['cache']->store('responsecache');
            });

        $this->app->singleton('semok.middleware.responsecache', ResponseCache::class);


        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ClearExpired::class,
                Commands\Clear::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        Config::set(
            'cache.stores.responsecache',
            [
                'driver' => 'file',
                'path' => storage_path('semok/responsecache'),
            ]
        );
    }
}
