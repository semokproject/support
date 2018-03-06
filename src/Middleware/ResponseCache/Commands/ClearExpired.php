<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use File;
use Exception;

class ClearExpired extends Command
{
    protected $signature = 'semok:responsecache:clear-expired';

    protected $description = 'Clear all expired response cache';

    protected $expired_file_count = 0;
    protected $active_file_count = 0;

    public function handle()
    {
        $directories = File::directories(storage_path('semok/cache'));
        foreach ($directories as $directory) {
            if (File::exists($directory . '/response')) {
                $this->deleteExpired($directory . '/response');
            }
        }

        $this->line('Total expired responsecache files removed: ' . $this->expired_file_count);
        $this->line('Total active responsecache files remaining: ' . $this->active_file_count);
    }

    protected function deleteExpired($dir)
    {
        // Grab the cache files
        $files = File::allFiles($dir);
        // Loop the files and get rid of any that have expired
        foreach ($files as $key => $cachefile) {
            // Ignore this file
            if($cachefile == '.gitignore') {
                continue;
            }

            try {
                // Grab the contents of the file
                $contents = File::get($cachefile);

                // Get the expiration time
                $expire = substr($contents, 0, 10);

                // See if we have expired
                if (time() >= $expire) {
                    // Delete the file
                    $dirName = substr($cachefile, 0, 2);
                    if (File::exists($dirName)) {
                        File::deleteDirectory($dirName);
                    } else {
                        File::delete($cachefile);
                    }

                    $this->expired_file_count++;
                } else {
                    $this->active_file_count++;
                }
            } catch(FileNotFoundException $e) {
                // Getting an occasional error of this type on the 'get' command above,
                // so adding a try-catch to skip the file if we do.
            }
        }
    }
}
