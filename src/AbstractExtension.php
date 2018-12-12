<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;

abstract class AbstractExtension  {

    protected $observer;

    abstract public static function name();

    abstract public function setup();

    abstract public function register(array $configs = [], Container $container);

    abstract public function compile(array $configs = [], Container $container);

    public static function metadata() : array 
    {
        return array();
    }

    public function setObserver(Observer $observer)
    {
        $this->observer = $observer;
        return $this;
    }

    public function getObserver() : Observer 
    {
        return $this->observer;
    }

    public function load(string $observerName, $parameter, array $options = [])
    {
         $options = array_replace_recursive([
            'extensionType' => 'package',
            'extensionName' => static::name(),
        ], static::metadata(), $options);
        return $this->getObserver()->publish($observerName, $parameter, $options);
    }

    public function observer($className, $priority = 100)
    {
        return $this->getObserver()->addObserver($className, $priority);
    }

    public function configTemplate(): array 
    {
        return [];
    }
    
}
