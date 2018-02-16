<?php

namespace Semok\Support\Sluggable;

use Semok\Support\Sluggable\Services\SlugService;
use Illuminate\Support\ServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Semok\Support\Sluggable
 */
class TheServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/resources/config/sluggable.php' => config_path('semok/sluggable.php'),
            ], 'semok.config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/resources/config/sluggable.php', 'semok.sluggable');
        $this->app->singleton(SluggableObserver::class, function($app) {
            return new SluggableObserver(new SlugService(), $app['events']);
        });
    }
}
