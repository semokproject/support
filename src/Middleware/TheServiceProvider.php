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
                __DIR__ . '/resources/config/domainfilter.php' => config_path('semok/middleware/domainfilter.php'),
                __DIR__ . '/resources/config/pagespeed.php' => config_path('semok/middleware/pagespeed.php'),
                __DIR__ . '/resources/config/responsecache.php' => config_path('semok/middleware/responsecache.php'),
            ], 'semok.config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/resources/config/domainfilter.php', 'semok.middleware.domainfilter');
        $this->mergeConfigFrom(__DIR__ . '/resources/config/pagespeed.php', 'semok.middleware.pagespeed');
        $this->mergeConfigFrom(__DIR__ . '/resources/config/responsecache.php', 'semok.middleware.responsecache');
        $middlewares = [
            DomainFilter::class
        ];
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
            $mg = config('laravel-page-speed.enabled',[]);
            foreach ($groups as $key => $value) {
                if (in_array($key, $mg)) {
                    $middlewares[] = $value;
                }
            }
        }
        $router = $this->app['router'];
        $router->middlewareGroup('public', $middlewares);
    }

    protected function registerPageSpeedMiddleware()
    {
        $pagespeed = $this->app['config']->get('semok.middleware.pagespeed');
        if (!isset($pagespeed['enabled']) || !is_array($pagespeed['enabled']) || empty($pagespeed['enabled'])){
            return false;
        }

        $pagespeed['enable'] = true;
        $this->app['config']->set(
            'laravel-page-speed',
            $pagespeed
        );
        $this->app->register(new \RenatoMarinho\LaravelPageSpeed\ServiceProvider($this->app));
        return true;
    }
}
