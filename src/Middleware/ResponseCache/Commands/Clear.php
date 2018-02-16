<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use Illuminate\Console\Command;
use Semok\Support\Middleware\ResponseCache\ResponseCacheRepository;
use Semok\Support\Middleware\ResponseCache\Events\ClearedResponseCache;
use Semok\Support\Middleware\ResponseCache\Events\FlushedResponseCache;
use Semok\Support\Middleware\ResponseCache\Events\ClearingResponseCache;
use Semok\Support\Middleware\ResponseCache\Events\FlushingResponseCache;

class Clear extends Command
{
    protected $signature = 'semok:responsecache:clear';

    protected $description = 'Clear the response cache';

    public function handle(ResponseCacheRepository $cache)
    {
        event(new FlushingResponseCache());
        event(new ClearingResponseCache());

        $cache->clear();

        event(new FlushedResponseCache());
        event(new ClearedResponseCache());

        $this->info('Response cache cleared!');
    }
}
