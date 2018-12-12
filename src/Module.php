<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\Module as ModuleInterface;
use Nusantara\Contract\Kernel\ModuleExtension;
use Nusantara\Contract\Kernel\Observer;
use Nusantara\Exception\CommonException;
use Nusantara\Exception\InvalidPackageException;
use Nusantara\Exception\InvalidModuleException;
use Nusantara\Component\Metadata\Reflection;
use Nusantara\Component\Collection\ArrayCollection;
use Nusantara\Component\Collection\Configuration;
use Nusantara\Traits\ContainerAwareTrait;

class Module implements ModuleInterface {

    use ContainerAwareTrait;

    protected $modules;

    protected $observer;

    public function __construct(Container $container, Observer $observer)
    {
        $this->observer = $observer;

        $this->modules = new ArrayCollection();

        $this->container = $container;
    }

    public function boot()
    {
        foreach ($this->modules->all() as $name => $value) {
            $instance = $this->get($name);
            if(is_callable(array($instance, 'compile')))
            {
                call_user_func_array(array($instance, 'compile'), array([], $this->getContainer()));
            }
        }
    }


     public function add($instance, array $configs = [])
    {
        if(!Reflection::hasInterface($instance, ModuleExtension::class))
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
                    'modules' => [
                        'storage' => $parameters
                    ]
                ], $this->getParameters());

                asort($parameters);

                $parameters = Configuration::replace($parameters)->toArray();

                $configs = $parameters['modules']['storage'];
            }
        }

        call_user_func_array(array($instance, 'setObserver'), array($this->observer));

        call_user_func_array(array($instance, 'setup'), array());

        if(is_callable(array($instance, 'register')))
        {
            $instance->register($configs, $this->getContainer());
        }

        $this->registerInstance($this->moduleName($name), $instance);
        $this->modules->set($name, $this->moduleName($name));

        return $this;
    }

    public function get(string $name)
    {
        return $this->getInstance($this->moduleName($name));
    }

    public function moduleName(string $name)
    {
        return "modules.".$name.".extension";
    }

    public function __debugInfo() {
        return [
            'modules' => $this->modules
        ];
    }

}