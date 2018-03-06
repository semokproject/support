<?php

namespace Semok\Support\Cache;

use Illuminate\Support\Facades\Facade;

class TheFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'semok.cache';
    }
}
