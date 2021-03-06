<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;
use Semok\Support\Theme\Manifest;

class CreateTheme extends BaseCommand
{
    protected $signature = 'semok:theme:create {themeName?}';
    protected $description = 'Create a new theme';

    public function info($text,$newline = true)
    {
        $this->output->write("<info>$text</info>", $newline);
    }

    public function handle()
    {

        // Get theme name
        $themeName = $this->argument('themeName');
        if (!$themeName) {
            $themeName = $this->ask('Give theme name:');
        }

        // Check that theme doesn't exist
        if ($this->themeInstalled($themeName)) {
            $this->error("Error: Theme $themeName already exists");
            return;
        }

        // Read theme paths
        $viewsPath = $this->anticipate("Where will views be located [Default='$themeName']?", [$themeName]);

        // Calculate Absolute paths
        $viewsPathFull = semok_themes_path($viewsPath);

        // Ask for parent theme
        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')) {
            $themes = array_map(function($theme){
                return $theme->name;
            }, SemokTheme::all());
            $parentTheme = $this->choice('Which one', $themes);
        }

        $customConfiguration = $this->askCustomConfiguration();

        // Display a summary
        $this->info("Summary:");
        $this->info("- Theme name: ".$themeName);
        $this->info("- Views Path: ".$viewsPathFull);
        $this->info("- Extends Theme: ".($parentTheme ?: "No"));

        if (!empty($customConfiguration)) {
            $this->info("Custom Theme Configuration:");
            foreach ($customConfiguration as $key => $value) {
                $this->info("- $key: ".print_r($value, true));
            }
        }

        if ($this->confirm('Create Theme?', true)) {

            $themeJson = new Manifest(array_merge([
                "name"        => $themeName,
                "extends"     => $parentTheme,
                // "views-path"  => $viewsPath,
            ], $customConfiguration));

            // Create Paths + copy theme.json
            $this->files->makeDirectory($viewsPathFull);

            $themeJson->saveToFile(semok_themes_path("$viewsPath/theme.json"));

            // Rebuild Themes Cache
            SemokTheme::rebuildCache();
        }
    }


    // You can add request more information during theme setup. Just override this class and implement
    // the following method. It should return an associative array which will be appended
    // into the 'theme.json' configuration file. You can retreive this values
    // with Theme::getSetting('key') at runtime. You may optionaly want to redifine the
    // command signature too.
    public function askCustomConfiguration()
    {
        return [
            // 'key' => 'value',
        ];
    }

}
