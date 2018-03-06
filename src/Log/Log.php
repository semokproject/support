<?php

namespace Semok\Support\Log;

use Psr\Log\LoggerInterface;;

class Log
{
    protected $filename = 'semok';

    public function file($filename)
    {
        $this->filename = $filename;
        return $this;
    }

	public function __call($name, $arguments)
	{
        if (
            in_array($name, [
                'emergency',
                'alert',
                'critical',
                'error',
                'warning',
                'notice',
                'info',
                'debug'
            ])
        ) {
            $log = app()->make(LoggerInterface::class);
            $log->useFiles(
                storage_path('logs/' . $this->filename . '.' . $name . '.log'),
                config('app.log_level', 'debug')
            );
            $handlers = $log->getMonolog()->getHandlers();
            $handler = array_shift($handlers);
            $handler->setBubble(false);
            return call_user_func_array([$log, $name], $arguments);
        }
	}
}
