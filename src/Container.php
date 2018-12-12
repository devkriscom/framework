<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container as ContainerInterface;
use Nusantara\Component\Collection\ArrayCollection;
use Nusantara\Concrete;

class Container implements ContainerInterface {

	protected $contracts;

	protected $bootables;

	protected $aggregates;

	public function __construct()
	{
		$this->contracts = new ArrayCollection();
		$this->bootables = new ArrayCollection();
		$this->aggregates = new ArrayCollection();
	}

	public function contracts()
	{
		return $this->contracts;
	}

	public function bootables()
	{
		return $this->bootables;
	}

	public function aggregates()
	{
		return $this->aggregates;
	}

	public function register($module, $contract = null, $singleton = false)
	{
		if(is_string($module) && !(class_exists($module) || function_exists($module)))
		{
			throw new \Exception(sprintf("%s not valid module. An module must but valid class/functions", $module));
		}

		if((is_callable($module) || (is_string($module) && function_exists($module)) || is_object($module)) && is_null($contract))
		{
			throw new \Exception(sprintf("you need to provide alias name"));
		}

		/**
		 * Only check if class 
		 */
		if(is_null($contract) && is_string($module))
		{
			$contract = $module;
		}

		if($singleton)
		{
			$this->bootables->set($contract, $contract);
		}

		return $this->add($contract, $module);
	}

	public function resolve(string $name, $new = false)
	{
		if($this->contracts()->has($name))
		{
			$module = $this->aggregates()->get($this->contracts()->get($name));

		} elseif($this->aggregates()->has($name)) {

			$module = $this->aggregates()->get($name);

		} else {

			throw new \Exception(sprintf('Alias (%s) not exist in container.', $name));
		}

		return $module->resolve($new);	
	}

	public function add(string $name, $module) {

		if (!$module instanceof Concrete) {
			$module = new Concrete($this, $name, $module);
		}

		$this->aggregates->set($name, $module);

		return $module;
	}

	public function contract(string $contract, string $module) {

		$this->aggregates->set($contract, $module);
		return $this;
	}

	public function bind(string $name, $module) 
	{
		if(is_string($module)) {
			return $this->add($name, $module);
		}
		return $this->add($name, $module);
	}

	public function singleton(string $name, $module) 
	{
		$this->bootables->set($name, $name);
		return $this->add($name, $module)->setBootable(true);
	}

	public function get($name, $default = NULL)
	{

		if($this->contracts()->has($name))
		{
			$module = $this->aggregates()->get($this->contracts()->get($name));
		}

		if($this->aggregates()->has($name))
		{
			$module = $this->aggregates()->get($name);
		}

		try {
			return $module;	
		} catch (\Exception $e) {
			throw new \Exception(sprintf('Alias (%s) not exist in container.', $name));
		}
	}

	public function has($name): bool
	{
		if($this->contracts()->has($name))
		{
			return true;
		}

		if($this->aggregates()->has($name))
		{
			return true;
		}
		return false;
	}

	public function make(string $name, $new = false)
	{
		if($this->contracts()->has($name))
		{
			$module = $this->aggregates()->get($this->contracts()->get($name));

		} elseif($this->aggregates()->has($name)) {

			$module = $this->aggregates()->get($name);

		} else {

			throw new \Exception(sprintf('Alias (%s) not exist in container.', $name));

		}

		return $module->resolve($new);	
	}

	public function __call($method, $arguments)
	{
		if($this->has($method))
		{
			$new = false;
			if(count($arguments) > 0 && is_bool($arguments[0]))
			{
				$new = $arguments[0];
			}
			return $this->make($method, $new);
		}
	}

	public function boot()
	{
		foreach ($this->bootables()->all() as $key => $module) {
			$module = $this->aggregates()->get($module);
			$module->resolve();
		}
	}
}