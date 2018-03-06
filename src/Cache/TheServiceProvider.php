<?php

namespace Semok\Support\Cache;

use Config;
use Illuminate\Support\ServiceProvider;

class TheServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('semok.cache', function ($app) {
            return new Cache($app['cache']);
        });
    }
}
