<?php

namespace Semok\Support\Middleware\ResponseCache;

use Illuminate\Support\Facades\Facade;

class TheFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \Semok\Support\Middleware\ResponseCache\ResponseCache
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'semok.middleware.responsecache';
    }
}
