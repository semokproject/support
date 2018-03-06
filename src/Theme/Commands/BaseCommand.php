<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class BaseCommand extends Command
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->tempPath = $this->packagesPath('tmp');
        $this->files = $files;
    }

    protected function createTempFolder()
    {
        $this->clearTempFolder();
        $this->files->makeDirectory($this->tempPath);
    }

    protected function clearTempFolder()
    {
        if ($this->files->exists($this->tempPath)) {
            $this->files->deleteDirectory($this->tempPath);
        }
    }

    protected function packagesPath($path='')
    {
        return storage_path("themes/$path");
    }

    protected function themeInstalled($themeName)
    {
        if (!SemokTheme::exists($themeName)) {
            return false;
        }

        $viewsPath = SemokTheme::find($themeName)->viewsPath;
        return $this->files->exists(themes_path("$viewsPath/theme.json"));
    }
}
