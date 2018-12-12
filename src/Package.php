<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\Package as PackageInterface;
use Nusantara\Contract\Kernel\PackageExtension;
use Nusantara\Contract\Kernel\Observer;
use Nusantara\Component\Metadata\Reflection;
use Nusantara\Component\Collection\ArrayCollection;
use Nusantara\Component\Collection\Configuration;
use Nusantara\Traits\ContainerAwareTrait;

class Package implements PackageInterface {

    use ContainerAwareTrait;

    protected $packages;

    protected $observer;

    public function __construct(Container $container, Observer $observer)
    {
        $this->observer = $observer;

        $this->packages = new ArrayCollection();

        $this->container = $container;
    }

    public function boot()
    {
   
        foreach ($this->packages->all() as $name => $value) {
            $instance = $this->get($name);
            if(is_callable(array($instance, 'compile')))
            {
                call_user_func_array(array($instance, 'compile'), array([], $this->getContainer()));
            }
        }
    }

    public function add($instance, array $configs = [])
    {
        if(!Reflection::hasInterface($instance, PackageExtension::class))
        {
            InvalidModuleException::forInvalidModule($instance, PackageExtension::class);
        }

        if(is_string($instance) && class_exists($instance)) {
            $instance = new $instance;
        } 

        if(is_callable(array($instance, 'name')))
        {
            $name = $instance::name();
            if(!is_string($name))
            {
                InvalidPackageException::forInvalidName($instance);
            }
        }

        $configs = [];

        if(is_callable(array($instance, 'configPath')))
        {
            $configFile = $instance::configPath();

            if(is_string($configFile) && false !== $path = realpath($configFile))
            {   
                $parameters = Configuration::parse($path);
                $parameters =  array_replace_recursive([
                    'packages' => [
                        'storage' => $parameters
                    ]
                ], $this->getParameters());

                asort($parameters);

                $parameters = Configuration::replace($parameters)->toArray();

                $configs = $parameters['packages']['storage'];
            }
        }

        call_user_func_array(array($instance, 'setObserver'), array($this->observer));

        call_user_func_array(array($instance, 'setup'), array());

        if(is_callable(array($instance, 'register')))
        {
            $instance->register($configs, $this->getContainer());
        }



        $this->registerInstance($this->packageName($name), $instance);
        $this->packages->set($name, $this->packageName($name));

        return $this;
    }

    public function get(string $name)
    {
        return $this->getInstance($this->packageName($name));
    }

    public function packageName(string $name)
    {
        return "packages.".$name.".extension";
    }

    public function __debugInfo() {
        return [
            'packages' => $this->packages
        ];
    }
}