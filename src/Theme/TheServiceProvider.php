<?php

namespace Semok\Support\Theme;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Blade;

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
                \Semok\Support\Theme\Commands\listThemes::class,
                \Semok\Support\Theme\Commands\createTheme::class,
                \Semok\Support\Theme\Commands\removeTheme::class,
                \Semok\Support\Theme\Commands\createPackage::class,
                \Semok\Support\Theme\Commands\installPackage::class,
                \Semok\Support\Theme\Commands\refreshCache::class,
            ]);
        }
        $themes = $this->app->make('semok.themes');
        $themes->scanThemes();
	}
}
