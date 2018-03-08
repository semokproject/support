<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem as File;

class RemoveTheme extends BaseCommand
{
    protected $signature = 'semok:theme:remove {themeName?} {--force}';
    protected $description = 'Removes a theme';

    public function handle()
    {
        // Get theme name
        $themeName = $this->argument('themeName');
        if ($themeName == "") {
            $themes = array_map(function($theme){
                return $theme->name;
            }, SemokTheme::all());
            $themeName = $this->choice('Select a theme to create a distributable package:', $themes);
        }

        // Remove without confirmation?
        $force = $this->option('force');

        // Check that theme exists
        if (!SemokTheme::exists($themeName)) {
            $this->error("Error: Theme $themeName doesn't exist");
            return;
        }

        // Get the theme
        $theme = SemokTheme::find($themeName);

        // Diaplay Warning
        if (!$force) {
            $viewsPath = semok_themes_path($theme->viewsPath);

            $this->info("Warning: These folders will be deleted:");
            $this->info("- views: $viewsPath");

            if(!$this->confirm("Continue?"))
                return;
        }

        // Delete folders
        $theme->uninstall();
        $this->info("Theme $themeName was removed");

    }
}
