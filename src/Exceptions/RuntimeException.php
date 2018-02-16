<?php

namespace Semok\Support\Exceptions;

use Log;
use Exception as BaseException;

class RuntimeException extends BaseException
{
    protected $filename = 'semok.log';

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        Log::useDailyFiles(storage_path() . '/logs/' . $this->filename);
        Log::error($message);
        parent::__construct($message, $code, $previous);
    }
}
