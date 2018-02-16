<?php

namespace Semok\Support\Theme\Commands;

use Theme;
use Illuminate\Console\Command;

class ListThemes extends BaseCommand
{
    // protected $signature = 'namespace:command {argument?} {--option}';
    protected $signature = 'semok:theme:list';
    protected $description = 'List installed themes';

    public function info($text,$newline = true)
    {
        $this->output->write("<info>$text</info>", $newline);
    }

    public function handle()
    {
        $themes = Theme::all();
        $this->info('+----------------------+----------------------+----------------------+----------------------+');
        $this->info('|      Theme Name      |        Extends       |      Views Path      |      Asset Path      |');
        $this->info('+----------------------+----------------------+----------------------+----------------------+');
        foreach ($themes as $theme) {
            $this->info(sprintf("| %-20s | %-20s | %-20s | %-20s |",
                $theme->name,
                $theme->getParent() ? $theme->getParent()->name : "",
                $theme->viewsPath,
                $theme->assetPath
            ));
        }
        $this->info('+----------------------+----------------------+----------------------+----------------------+');
        $this->info('Views Path is relative to: '.themes_path());
        $this->info('Asset Path is relative to: '.public_path());
    }
}
