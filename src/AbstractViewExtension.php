<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Kernel;
use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\ModuleExtension;

abstract class AbstractViewExtension extends AbstractExtension implements ModuleExtension {

    public function load(string $observerName, $parameter, array $options = [])
    {
         $options = array_replace_recursive([
            'extensionType' => 'view',
            'extensionName' => static::name(),
        ], static::metadata(), $options);
         
        return $this->getObserver()->publish($observerName, $parameter, $options);
    }

}
