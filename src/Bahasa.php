<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Kernel;
use Nusantara\Component\Collection\Configuration;

class Bahasa implements Kernel {
	
	protected $package;

	protected $module;

	protected $observer;

	protected $service;

	protected $database;

	protected $storage;

	protected $connect;

	protected $cache;

	protected $mailer;

	protected $container;

	public function __construct($setup = null) {

		$container = new Container();

		$container->register(new Parameter(), 'parameter');

		$this->observer = new Observer($container);

		$this->package = new Package($container, $this->observer);

		$this->module = new Module($container, $this->observer);

		$this->container = $container;

		if($setup && realpath($setup))
		{
			$config = Configuration::load($setup)->toArray();

	
		}
	}

	public function container()
	{
		return $this->container;
	}

	public function loadConfig($path)
	{
		return $this->container->make('parameter')->load($path);
	}

	public function registerPackage(string $instance, array $configs = [])
	{
		if(is_string($instance) && class_exists($instance))
		{
			$instance = new $instance;
			if(is_callable(array($instance, 'expandKernel')))
			{
				call_user_func_array(array($instance, 'expandKernel'), array($this));
			}
		}
		$this->package->add($instance, $configs);
		return $this;
	}

	public function registerService(string $instance, array $configs = [])
	{
		return $this->module($instance, $configs);
	}

	public function registerView(string $instance, array $configs = [])
	{
		return $this->module($instance, $configs);
	}

	
	public function module(string $instance, array $configs = [])
	{
		$this->module->add($instance, $configs);
		return $this;
	}


	public function service(string $service)
	{
		return $this->service->manager($service);
	}

	public function database()
	{
		return $this->database;
	}

	public function storage()
	{
		return $this->storage;
	}

	public function cache()
	{
		return $this->cache;
	}

	public function mailer()
	{
		return $this->mailer;
	}

	public function compile() 
	{
		$this->observer->boot();
		
		$this->package->boot();

		$this->module->boot();

		$this->container->boot();

		if($this->container->has(\Nusantara\Contract\Service::class))
		{
			$this->service = $this->container->resolve(\Nusantara\Contract\Service::class);
		}

		if($this->container->has(\Nusantara\Contract\Database::class))
		{
			$this->database = $this->container->resolve(\Nusantara\Contract\Database::class);
		}

		if($this->container->has(\Nusantara\Contract\Storage::class))
		{
			$this->storage = $this->container->resolve(\Nusantara\Contract\Storage::class);
		}

		if($this->container->has(\Nusantara\Contract\Cache::class))
		{
			$this->cache = $this->container->resolve(\Nusantara\Contract\Cache::class);
		}

		if($this->container->has(\Nusantara\Contract\Connect::class))
		{
			$this->connect = $this->container->resolve(\Nusantara\Contract\Connect::class);
		}

		if($this->container->has(\Nusantara\Contract\Mailer::class))
		{
			$this->mailer = $this->container->resolve(\Nusantara\Contract\Mailer::class);
		}

		return $this;
	}

}