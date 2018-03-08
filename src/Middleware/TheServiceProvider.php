<?php

namespace Semok\Support\Middleware;

use Illuminate\Support\ServiceProvider;
use RenatoMarinho\LaravelPageSpeed\Middleware\TrimUrls;
use RenatoMarinho\LaravelPageSpeed\Middleware\InlineCss;
use RenatoMarinho\LaravelPageSpeed\Middleware\RemoveQuotes;
use RenatoMarinho\LaravelPageSpeed\Middleware\RemoveComments;
use RenatoMarinho\LaravelPageSpeed\Middleware\ElideAttributes;
use RenatoMarinho\LaravelPageSpeed\Middleware\InsertDNSPrefetch;
use RenatoMarinho\LaravelPageSpeed\Middleware\CollapseWhitespace;
use Semok\Support\Middleware\ResponseCache\Middlewares\CacheResponse;
use RenatoMarinho\LaravelPageSpeed\ServiceProvider as LaravelPageSpeedServiceProvider;
use Semok\Support\Middleware\ResponseCache\TheServiceProvider as ResponseCacheServiceProvider;

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
        $middlewares = [DomainFilter::class];
        if ($this->app['config']->get('semok.middleware.responsecache.enabled')) {
            $this->app->register(new ResponseCacheServiceProvider($this->app));
            $middlewares[] = CacheResponse::class;
        }
        if ($pageSpeeds = $this->registerPageSpeedMiddleware()) {
            $groups = [
                'trim-urls' => TrimUrls::class,
                'inline-css' => InlineCss::class,
                'remove-quotes' => RemoveQuotes::class,
                'remove-comments' => RemoveComments::class,
                'elide-attributes' => ElideAttributes::class,
                'insert-dns-prefetch' => InsertDNSPrefetch::class,
                'collapse-whitespace' => CollapseWhitespace::class
            ];
            $mg = config('laravel-page-speed.enabled',[]);
            foreach ($groups as $key => $value) {
                if (in_array($key, $mg)) {
                    $middlewares[] = $value;
                }
            }
        }
        $router = $this->app['router'];
        $router->middlewareGroup('semok', $middlewares);
    }

    protected function registerPageSpeedMiddleware()
    {
        if (env('APP_DEBUG')) {
            return false;
        }

        $pagespeed = $this->app['config']->get('semok.middleware.pagespeed');
        if (!isset($pagespeed['enabled']) || !is_array($pagespeed['enabled']) || empty($pagespeed['enabled'])) {
            return false;
        }
        $pagespeed['enable'] = true;
        $this->app['config']->set(
            'laravel-page-speed',
            $pagespeed
        );
        $this->app->register(new LaravelPageSpeedServiceProvider($this->app));
        return true;
    }
}
