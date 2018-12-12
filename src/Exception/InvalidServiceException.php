<?php
declare (strict_types = 1);

namespace Nusantara\Exception;

use Nusantara\Contract\Middleware;
use Nusantara\Contract\Exception;
class InvalidServiceException extends \InvalidArgumentException implements Exception
{
    public static function forInvalidService($service, $manager)
    {
        $name = is_object($service) ? get_class($service) : gettype($service);
        $manager = is_object($manager) ? get_class($manager) : $manager;
        $message = sprintf(
            'Cannot add "%s" to service manager as it does not have valid service or class  : %s.',
            $name, $manager 
        );
        return new static($message);
    }
}