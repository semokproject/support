<?php

namespace Semok\Support\Middleware\ResponseCache\Exceptions;

use Semok\Support\Exceptions\RuntimeException;

class CouldNotUnserialize extends RuntimeException
{
    public static function serializedResponse(string $serializedResponse): self
    {
        return new static("Could not unserialize `{$serializedResponse}`");
    }
}
