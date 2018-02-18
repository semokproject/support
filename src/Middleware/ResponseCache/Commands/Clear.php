<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use Illuminate\Console\Command;
use Semok\Support\Middleware\ResponseCache\ResponseCacheRepository;

class Clear extends Command
{
    protected $signature = 'semok:responsecache:clear';

    protected $description = 'Clear the response cache';

    public function handle(ResponseCacheRepository $cache)
    {
        $cache->clear();

        $this->info('Response cache cleared!');
    }
}
