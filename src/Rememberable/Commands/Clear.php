<?php

namespace Semok\Support\Rememberable\Commands;

use File;
use Illuminate\Console\Command;

class Clear extends Command
{
    protected $signature = 'semok:rememberable:clear';

    protected $description = 'Clear the rememberable cache';

    public function handle()
    {
        $cache = app('cache');
        if ( ! method_exists($cache->getStore(), 'tags')) {
            $directories = File::directories(storage_path('semok/cache'));
            foreach ($directories as $directory) {
                if (File::exists($directory . '/rememberable')) {
                    File::deleteDirectory($directory . '/rememberable');
                }
            }
        } else {
            $cache->tags('rememberable')->flush();
        }

        $this->info('Rememberable cache cleared');
    }
}
