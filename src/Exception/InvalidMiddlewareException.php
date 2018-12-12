<?php
declare (strict_types = 1);

namespace Nusantara\Exception;

use Nusantara\Contract\Middleware;
use Nusantara\Contract\Exception;
class InvalidMiddlewareException extends \InvalidArgumentException implements Exception
{
    public static function forMiddleware($middleware)
    {
        $name = is_object($middleware) ? get_class($middleware) : gettype($middleware);
        $message = sprintf(
            'Cannot add "%s" to middleware as it does not implement the Middleware interface : %s.',
            $name, Middleware::class 
        );
        return new static($message);
    }
}