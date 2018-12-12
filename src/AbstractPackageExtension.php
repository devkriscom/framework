<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\PackageExtension;

abstract class AbstractPackageExtension extends AbstractExtension implements PackageExtension {

    public function load(string $observerName, $parameter, array $options = [])
    {
         $options = array_replace_recursive([
            'extensionType' => 'package',
            'extensionName' => static::name(),
        ], static::metadata(), $options);
         
        return $this->getObserver()->publish($observerName, $parameter, $options);
    }

}

