<?php

namespace Semok\Support\Exceptions;

use Exception;

class RuntimeException extends Exception
{
    public $filename = 'semok.error.log';

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
