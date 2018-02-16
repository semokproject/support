<?php

namespace Semok\Support\Middleware\ResponseCache;

use Illuminate\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Semok\Support\Middleware\ResponseCache\Commands\Clear;
use Semok\Support\Middleware\ResponseCache\Commands\Flush;
use Semok\Support\Middleware\ResponseCache\CacheProfiles\CacheProfile;

class TheServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->bind(CacheProfile::class, function (Application $app) {
            return $app->make(config('semok.middleware.responsecache.cache_profile'));
        });

        $this->app->when(ResponseCacheRepository::class)
            ->needs(Repository::class)
            ->give(function (): Repository {
                $repository = $this->app['cache']->store(config('semok.middleware.responsecache.cache_store'));
                if (! empty(config('semok.middleware.responsecache.cache_tag'))) {
                    return $repository->tags(config('semok.middleware.responsecache.cache_tag'));
                }

                return $repository;
            });

        $this->app->singleton('semok.middleware.responsecache', ResponseCache::class);

        $this->app['command.semok.middleware.responsecache:flush'] = $this->app->make(Flush::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Flush::class,
                Clear::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //$this->mergeConfigFrom(__DIR__.'/../config/responsecache.php', 'responsecache');
    }
}
