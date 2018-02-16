<?php

namespace Semok\Support\Theme;

use Illuminate\Support\Facades\Facade;

class TheFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'semok.themes';
    }
}
