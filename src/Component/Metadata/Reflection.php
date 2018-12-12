<?php
declare (strict_types = 1);

namespace Nusantara\Component\Metadata;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionType;

class Reflection
{

	public static function hasInterface($className, $interface) : bool
	{
		if(is_object($className))
		{
			$className = get_class($className);
		}
		$class = new ReflectionClass($className);
		if ($class->implementsInterface($interface)) {
			return true;
		}
		return false;
	}
	
}