<?php

namespace Semok\Support\Log;

use Illuminate\Support\Facades\Facade;

class TheFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'semok.log';
    }
}
