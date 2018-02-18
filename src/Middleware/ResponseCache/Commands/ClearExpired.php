<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Config;
use Storage;
use Exception;

class ClearExpired extends Command
{
    protected $signature = 'semok:responsecache:clear-expired';

    protected $description = 'Clear all expired response cache';

    public function handle()
    {
        $cacheDisk = [
            'driver' => 'local',
            'root' => config('cache.stores.responsecache.path')
        ];
        Config::set('filesystems.disks.responsecache', $cacheDisk);
        $expired_file_count = 0;
        $active_file_count = 0;

        // Grab the cache files
        $files = Storage::disk('responsecache')->allFiles();

        // Loop the files and get rid of any that have expired
        foreach ($files as $key => $cachefile) {
            // Ignore this file
            if($cachefile == '.gitignore') {
                continue;
            }

            try {
                // Grab the contents of the file
                $contents = Storage::disk('responsecache')->get($cachefile);

                // Get the expiration time
                $expire = substr($contents, 0, 10);

                // See if we have expired
                if (time() >= $expire) {
                    // Delete the file
                    $dirName = substr($cachefile, 0, 2);
                    if (Storage::disk('responsecache')->exists($dirName)) {
                        Storage::disk('responsecache')->deleteDirectory($dirName);
                    } else {
                        Storage::disk('responsecache')->delete($cachefile);
                    }

                    $expired_file_count++;
                } else {
                    $active_file_count++;
                }
            } catch(FileNotFoundException $e) {
                // Getting an occasional error of this type on the 'get' command above,
                // so adding a try-catch to skip the file if we do.
            }
        }

        $this->line('Total expired responsecache files removed: ' . $expired_file_count);
        $this->line('Total active responsecache files remaining: ' . $active_file_count);
    }
}
