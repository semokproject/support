<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;
use Semok\Support\Theme\Manifest;

class CreatePackage extends BaseCommand
{
    protected $signature = 'semok:theme:create-package {themeName?}';
    protected $description = 'Create a theme package';

    public function handle()
    {
        $themeName = $this->argument('themeName');

        if ($themeName == "") {
            $themes = array_map(function($theme) {
                return $theme->name;
            }, SemokTheme::all());
            $themeName = $this->choice('Select a theme to create a distributable package:', $themes);
        }
        $theme = SemokTheme::find($themeName);

        $viewsPath = semok_themes_path($theme->viewsPath);

        // Packages storage path
        $packagesPath = $this->packagesPath();
        if(!$this->files->exists($packagesPath))
            mkdir($packagesPath);

        // Sanitize target filename
        $packageFileName = $theme->name;
        $packageFileName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $packageFileName);
        $packageFileName = mb_ereg_replace("([\.]{2,})", '', $packageFileName);
        $packageFileName = $this->packagesPath("{$packageFileName}.theme.tar.gz");

        // Create Temp Folder
        $this->createTempFolder();

        // Copy Views+Assets to Temp Folder
        system("cp -r $viewsPath {$this->tempPath}/views");

        // Add viewsPath into theme.json file
        $themeJson = new Manifest();
        $themeJson->loadFromFile("{$this->tempPath}/views/theme.json");
        $themeJson->set('views-path', $theme->viewsPath);
        $themeJson->saveToFile("{$this->tempPath}/views/theme.json");

        // Tar Temp Folder contents
        system("cd {$this->tempPath} && tar -cvzf $packageFileName .");

        // Del Temp Folder
        $this->clearTempFolder();

        $this->info("Package created at [$packageFileName]");
    }
}
