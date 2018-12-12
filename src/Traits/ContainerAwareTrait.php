<?php
declare (strict_types = 1);

namespace Nusantara\Traits;

use Nusantara\Contract\Container;

trait ContainerAwareTrait
{

	public function setContainer(Container $container)
	{
		$this->container = $container;
		return $this;
	}

	public function getContainer(): Container
	{
		return $this->container;
	}

	public function getInstance(string $name)
	{
		return $this->getContainer()->resolve($name);
	}

	public function registerInstance(string $name, $concrete, array $arguments = [])
	{
		if($this->hasInstance($name))
		{
			return $this->getInstance($name);
		}
		return $this->addInstance($name, $concrete, $arguments);
	}

	public function addInstance(string $name, $instance, array $arguments = [])
	{
		$instance = $this->getContainer()->register($instance, $name);
		if(count($arguments) > 0)
		{
			$instance->setArguments($arguments);
		}
		return $instance;
	}

	public function hasInstance(string $name) : bool
	{
		return $this->getContainer()->has($name);
	}

	public function getParameters()
    {
        return $this->getContainer()->resolve('parameter')->all();
    }

	public function getConfig(string $name, $default = null)
    {
        return $this->getContainer()->resolve('parameter')->get($name, $default);
    }

    public function updateConfig($name, array $configs = [])
    {
        return $this->getContainer()->resolve('parameter')->set($name, $configs);
    }


}