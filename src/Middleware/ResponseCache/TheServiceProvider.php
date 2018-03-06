<?php

namespace Semok\Support\Middleware\ResponseCache;

use Illuminate\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Semok\Support\Middleware\ResponseCache\Commands\Clear;
use Semok\Support\Middleware\ResponseCache\Commands\ClearExpired;
use Semok\Support\Middleware\ResponseCache\CacheProfiles\CacheProfile;

class TheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function register()
    {
        $this->app->bind(CacheProfile::class, function (Application $app) {
            return $app->make(config('semok.middleware.responsecache.cache_profile'));
        });

        $this->app->when(ResponseCacheRepository::class)
            ->needs(Repository::class)
            ->give(function (): Repository {
                $cache_dir = config('semok.middleware.domainfilter.domain', 'default') . '/response';
                return $this->app['semok.cache']->dir($cache_dir);
            });

        $this->app->singleton('semok.middleware.responsecache', ResponseCache::class);


        $this->commands([
            ClearExpired::class,
            Clear::class,
        ]);
    }
}
