<?php

namespace Semok\Support\Theme;

use Illuminate\Support\Facades\Facade;

class TheFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'semok.themes';
    }
}
