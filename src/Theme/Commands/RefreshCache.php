<?php

namespace Semok\Support\Theme\Commands;

use SemokTheme;
use Illuminate\Console\Command;

class RefreshCache extends BaseCommand
{
    protected $signature = 'semok:theme:refresh-cache';
    protected $description = 'Rebuilds the cache of "theme.json" files for each theme';

    public function handle()
    {
        // Rebuild Themes Cache
        SemokTheme::rebuildCache();

        $this->info("Themes cache was refreshed. Currently theme caching is: " . (Theme::cacheEnabled() ? "ENABLED" : "DISABLED"));
    }
}
