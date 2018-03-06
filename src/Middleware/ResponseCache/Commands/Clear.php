<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use File;
use Illuminate\Console\Command;
use Semok\Support\Middleware\ResponseCache\ResponseCacheRepository;

class Clear extends Command
{
    protected $signature = 'semok:responsecache:clear';

    protected $description = 'Clear the response cache';

    public function handle(ResponseCacheRepository $cache)
    {
        $directories = File::directories(storage_path('semok/cache'));
        foreach ($directories as $directory) {
            if (File::exists($directory . '/response')) {
                File::deleteDirectory($directory . '/response');
            }
        }
        $this->info('Responcecache cleared');
    }
}
