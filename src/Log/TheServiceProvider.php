<?php

namespace Semok\Support\Log;

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
        $this->app->singleton('semok.log', function () {
            return new Log;
        });
    }
}
