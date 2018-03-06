<?php

namespace Semok\Support;

use Cocur\Slugify\Slugify;
use Illuminate\Support\ServiceProvider;
use Semok\Support\Sluggable\TheServiceProvider as SluggableServiceProvider;
use Semok\Support\Rememberable\Commands\Clear;

class TheServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/config/permalinks.php' => config_path('semok/permalinks.php'),
                __DIR__ . '/../resources/config/site.php' => config_path('semok/site.php'),
            ], 'semok.config');
        }
        $this->commands([Clear::class]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(new SluggableServiceProvider($this->app));
    }
}
