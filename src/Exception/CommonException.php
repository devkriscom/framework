<?php
declare (strict_types = 1);

namespace Nusantara\Exception;

use Nusantara\Contract\Exception;
class CommonException extends \Exception implements Exception
{

	public static function shouldImlementInterface($instance, $interface, $location)
    {
        $instance = is_object($instance) ? get_class($instance) : gettype($instance);
        $interface = is_object($interface) ? get_class($interface) : gettype($interface);
        $location = is_object($location) ? get_class($location) : gettype($location);
     
        return new static(sprintf(' %s should implements "%s" class : %s.', $instance, $interface, $location));
    }

    public static function forClassNotfound()
    {
    	
    }

}
