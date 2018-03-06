<?php

namespace Semok\Support\Theme;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Blade;
use Semok\Support\Theme\Commands\ListThemes;
use Semok\Support\Theme\Commands\CreateTheme;
use Semok\Support\Theme\Commands\RemoveTheme;
use Semok\Support\Theme\Commands\RefreshCache;
use Semok\Support\Theme\Commands\CreatePackage;
use Semok\Support\Theme\Commands\InstallPackage;

class TheServiceProvider extends ServiceProvider {

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/resources/config/themes.php', 'semok.themes');
        $this->app->singleton('semok.themes', function(){
			return new Themes();
		});

        $this->app->singleton('view.finder', function($app) {
            return new ViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
        });
    }

	public function boot(){
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/resources/config/themes.php' => config_path('semok/themes.php'),
            ], 'semok.config');
            $this->commands([
                ListThemes::class,
                CreateTheme::class,
                RemoveTheme::class,
                CreatePackage::class,
                InstallPackage::class,
                RefreshCache::class,
            ]);
        }
        $themes = $this->app->make('semok.themes');
        $themes->scanThemes();
	}
}
