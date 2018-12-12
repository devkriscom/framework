<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\ObserverExtension;

abstract class AbstractObserverExtension implements ObserverExtension {

	abstract public static function name();

	abstract public function resolve(Container $container, $parameters = [], array $metadata = []);

}