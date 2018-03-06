<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;
use Semok\Support\Theme\Manifest;
use Illuminate\Filesystem\Filesystem as File;

class InstallPackage extends BaseCommand
{
    protected $signature = 'semok:theme:install-package {package?}';
    protected $description = 'Install a theme package';

    public function handle()
    {
        $package = $this->argument('package');

        if (!$package) {
            $filenames = $this->files->glob($this->packagesPath('*.theme.tar.gz'));
            $packages = array_map(function($filename){
                return basename($filename, '.theme.tar.gz');
            }, $filenames);
            $package = $this->choice('Select a theme to install:', $packages);
        }
        $package = $this->packagesPath($package . '.theme.tar.gz');

        // Create Temp Folder
        $this->createTempFolder();

        // Untar to temp folder
        exec("tar xzf $package -C {$this->tempPath}");

        // Read theme.json
        $themeJson = new Manifest();
        $themeJson->loadFromFile("{$this->tempPath}/views/theme.json");

        // Check if theme is already installed
        $themeName = $themeJson->get('name');
        if ($this->themeInstalled($themeName)) {
            $this->error('Error: Theme ' . $themeName.' already exist. You must remove it first with "artisan theme:remove ' . $themeName . '"');
            $this->clearTempFolder();
            return;
        }

        // Target Paths
        $viewsPath = themes_path($themeJson->get('views-path'));

        // If Views+Asset paths don't exist, move theme from temp to target paths
        if (file_exists($viewsPath)) {
            $this->info("Warning: Views path [$viewsPath] already exists. Will not be installed.");
        } else {
            exec("mv {$this->tempPath}/views $viewsPath");

            // Remove 'theme-views' from theme.json
            $themeJson->remove('views-path');
            $themeJson->saveToFile("$viewsPath/theme.json");
            $this->info("Theme views installed to path [$viewsPath]");
        }

        // Rebuild Themes Cache
        SemokTheme::rebuildCache();

        // Del Temp Folder
        $this->clearTempFolder();
    }
}
