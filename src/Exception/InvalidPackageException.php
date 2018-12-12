<?php
declare (strict_types = 1);

namespace Nusantara\Exception;

use Nusantara\Contract\Middleware;
use Nusantara\Contract\Exception;
class InvalidPackageException extends \InvalidArgumentException implements Exception
{
    public static function forInvalidName($instance)
    {
        $name = is_object($instance) ? get_class($instance) : gettype($instance);
        $message = sprintf(
            'Cannot add "%s" to service manager as it does not have valid service or class  : %s.',$instance,$instance
        );
        return new static($message);
    }

    public static function forDuplicateName($name, $instance)
    {
        $name = is_object($instance) ? get_class($instance) : gettype($instance);
        $manager = is_object($manager) ? get_class($manager) : gettype($manager);
        $message = sprintf(
            'Cannot add "%s" to service manager as it does not have valid service or class  : %s.',
            $name, $manager 
        );
        return new static($message);
    }

    public static function forDuplicateInstall($name, $instance)
    {
        $name = is_object($instance) ? get_class($instance) : gettype($instance);
        $manager = is_object($manager) ? get_class($manager) : gettype($manager);
        $message = sprintf(
            'Cannot add "%s" to service manager as it does not have valid service or class  : %s.',
            $name, $manager 
        );
        return new static($message);
    }

}