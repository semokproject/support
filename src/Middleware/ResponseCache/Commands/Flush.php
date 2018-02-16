<?php

namespace Semok\Support\Middleware\ResponseCache\Commands;

use Illuminate\Console\Command;

class Flush extends Command
{
    protected $signature = 'semok:responsecache:flush';

    protected $description = 'Flush the response cache (deprecated - use the clear method)';

    public function handle()
    {
        $this->call('semok:responsecache:clear');
    }
}
