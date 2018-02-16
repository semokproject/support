<?php

namespace Semok\Support\Middleware;

use Illuminate\Support\ServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Semok\Support\Middleware
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
                __DIR__ . '/resources/config/middleware.php' => config_path('semok/middleware.php'),
            ], 'semok.config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/resources/config/middleware.php', 'semok.middleware');
        $middlewares = [];
        if ($this->app['config']->get('semok.middleware.responsecache.enabled')) {
            $this->app->register(new ResponseCache\TheServiceProvider($this->app));
            $middlewares[] = ResponseCache\Middlewares\CacheResponse::class;
        }
        if ($pageSpeeds = $this->registerPageSpeedMiddleware()) {
            $groups = [
                'inline-css' => \RenatoMarinho\LaravelPageSpeed\Middleware\InlineCss::class,
                'elide-attributes' => \RenatoMarinho\LaravelPageSpeed\Middleware\ElideAttributes::class,
                'insert-dns-prefetch' => \RenatoMarinho\LaravelPageSpeed\Middleware\InsertDNSPrefetch::class,
                'remove-comments' => \RenatoMarinho\LaravelPageSpeed\Middleware\RemoveComments::class,
                'trim-urls' => \RenatoMarinho\LaravelPageSpeed\Middleware\TrimUrls::class,
                'remove-quotes' => \RenatoMarinho\LaravelPageSpeed\Middleware\RemoveQuotes::class,
                'collapse-whitespace' => \RenatoMarinho\LaravelPageSpeed\Middleware\CollapseWhitespace::class
            ];
            $mg = config('laravel-page-speed.groups',[]);
            foreach ($groups as $key => $value) {
                if (in_array($key, $mg)) {
                    $middlewares[] = $value;
                }
            }
        }

        if(!is_array($middlewares) || empty($middlewares)){
            return;
        }
        $router = $this->app['router'];
        $router->middlewareGroup('public', $middlewares);
    }

    protected function registerPageSpeedMiddleware()
    {
        $pagespeed = $this->app['config']->get('semok.middleware.pagespeed');
        if(!$pagespeed['enabled']){
            return false;
        }
        $pagespeed['enable'] = true;
        $this->app['config']->set(
            'laravel-page-speed',
            $this->app['config']->get('semok.middleware.pagespeed', [])
        );
        $this->app->register(new \RenatoMarinho\LaravelPageSpeed\ServiceProvider($this->app));
        return true;
    }
}
