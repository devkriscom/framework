<?php
declare (strict_types = 1);

namespace Nusantara;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionFunctionAbstract;
use ReflectionParameter;
use ReflectionType;
use Nusantara\Contract\Container;
use Nusantara\Component\Metadata\Reflection;

class Concrete  {

	/**
	 * Immutable name
	 * @var string
	 */
	protected $name;

	/**
	 * this should beimmutable & unique
	 * @var string
	 */
	protected $contract;

	/**
	 * @var boolean
	 */
	protected $shared = false;

	/**
	 * @var array
	 */
	protected $arguments = [];

	/**
	 * @var array
	 */
	protected $tags = [];

	/**
	 * @var Closure|string|object
	 */
	protected $concrete;

	/**
	 * @var Object
	 */
	protected $resolved;

	/**
	 * @var boolean $bootable
	 */
	protected $bootable = false;

	/**
	 * @var Container $container
	 */
	protected $container;

	public function __construct(Container $container, string $name, $concrete)
	{
		$this->name    = $name;
		$this->ensureConcrete($concrete);
		$this->concrete = $concrete;
		$this->container = $container;
	}

	private function ensureConcrete($concrete)
	{
		$valid = false;
		if(is_callable($concrete) || is_object($concrete) || class_exists($concrete) || function_exists($concrete))
		{
			$valid = true;
		}
		if(!$valid)
		{
			throw new \Exception(sprintf("%s should be a valid class or function", $concrete));
		}

	}

	public function contract(string $name) {
		$this->container->contract($this->name, $name);
		return $this;
	}

	/**
	 * Initate object. 
	 * @param  bool|boolean $new create new instance
	 * @return object            
	 */
	public function resolve(bool $new = false)
	{
		$concrete = $this->getConcrete();

		try {

			if (!is_null($this->getResolved()) && $new === false) 
			{
				return $this->getResolved();
			}

			/**
			 * prevent bootable instance from re-initiate or re-create
			 */
			
			if (is_callable($concrete) && $concrete instanceof \Closure) 
			{
				$refFunc = new ReflectionFunction($concrete);
				$arguments = $this->extractFromParameters($refFunc->getParameters());
				$concrete = call_user_func_array($concrete, $arguments);

			} elseif(is_string($concrete) && class_exists($concrete)) {

				$reflection = new ReflectionClass($concrete);

				$arguments = [];

				if(method_exists($concrete, '__construct'))
				{
					$arguments = $reflection->getMethod('__construct')->getParameters();

					$arguments = $this->extractFromParameters($arguments);
				}

				$concrete = $reflection->newInstanceArgs($arguments);

			} elseif(is_string($concrete) && function_exists($concrete)) {

				/**
				 * Function should return a anonymous function
				 * @var ReflectionFunction
				 */
				$refFunc = new ReflectionFunction($concrete);
				$arguments = $this->extractFromParameters($refFunc->getParameters());

			

				//$concrete = call_user_func_array($concrete, $arguments);

			} elseif(is_object($concrete)) {
				//just continue for now

			} elseif(is_array($concrete) && count($concrete) > 0 && is_string($concrete) && class_exists($concrete[0])) {
				//just continue for now

			} else {

				throw new InvalidConcrete($concrete." not right");
			}

		} catch (\Exception $e) {
			echo $e->getMessage();
		}

		$this->setResolved($concrete);

		if(is_string($concrete)) {
			throw new \Exception(sprintf("%s concrete not right", $concrete));
		}

		return $concrete;
	}

	public function extractFromParameters(array $parameters = [])
	{
		$args = [];
		foreach ($parameters as $key => $par) {
			
			$required = $par->isOptional() ? false : true;
			$value    = '';

			if ($par->isDefaultValueAvailable()) {

				$value = $par->getDefaultValue();
				if (is_string($value) && strlen($value) > 15) {
					$value = substr($value, 0, 15) . '...';
				}

				if (is_double($value) && fmod($value, 1.0) === 0.0) {
					$value = (int)$value;
				}

				$value = str_replace('\\\\', '\\', var_export($value, true));

			}

			$args[] = array($par->getPosition(), (string)$par->getType(), $required, $value);
		}

		$arguments = [];

		if(count($args) > 0)
		{
			foreach ($args as $key => $argument) {

				list($position, $type, $required, $value) = $argument;

				if ('array' === $type) {

					$arguments[$position] = ($value && is_array($value)) ? $value : [];

				} elseif (is_string($type)) {

					if ($type === Container::class) {

						$arguments[$position] = Container::class;

					} elseif ( $this->container->has($type) ) {

						$arguments[$position] = $this->container->resolve($type);

					} elseif ( class_exists($type) ) {
						/**
						 * register class to container for future use
						 */
						$this->container->register($type);
						$arguments[$position] = $this->container->resolve($type);
					}

				} else {

					$arguments[$position] = $value;

				}
			}
		}

		$arguments = array_replace_recursive($arguments, $this->arguments);

		return $this->makeArguments($arguments);
	}

	public function makeArguments($arguments)
	{
		$args = [];
		foreach ($arguments as $arg) {
			if(is_string($arg) && $this->container->has($arg)) {
				$args[] = $this->container->resolve($arg);
			} else if (is_string($arg) && $arg === Container::class) {
				$args[] = $this->container;
			} else {
				$args[] = $arg;
			}
		}
		return $args;
	}

	public function __debugInfo() {
		return [
			'id' => $this->name,
			'contract' => $this->contract ?? $this->name,
			'module' => (is_object($this->concrete)) ? get_class($this->concrete) : $this->concrete,
			'resolved' => $this->resolved ? true : false,
			'shared' => $this->shared,
			'tags' => $this->tags
		];
	}



	public function getName(): string
	{
		return $this->name;
	}

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
    	$this->name = $name;

    	return $this;
    }

    /**
     * @return string
     */
    public function getContract()
    {
    	return $this->contract;
    }

    /**
     * @param string $contract
     *
     * @return self
     */
    public function setContract($contract)
    {
    	$this->contract = $contract;

    	return $this;
    }

    /**
     * @return boolean
     */
    public function isShared()
    {
    	return $this->shared;
    }

    /**
     * @param boolean $shared
     *
     * @return self
     */
    public function setShared($shared)
    {
    	$this->shared = $shared;

    	return $this;
    }

    /**
     * @return array
     */
    public function getArguments()
    {
    	return $this->arguments;
    }

    /**
     * @param array $arguments
     *
     * @return self
     */
    public function setArguments(array $arguments)
    {
    	$this->arguments = $arguments;

    	return $this;
    }

     /**
     * @param array $arguments
     *
     * @return self
     */
     public function addArgument($argument)
     {
     	$this->arguments[] = $argument;

     	return $this;
     }

    /**
     * @return array
     */
    public function getTags()
    {
    	return $this->tags;
    }

     /**
     * @param array $tags
     *
     * @return self
     */
     public function addTag($tag)
     {
     	$this->tags[] = $tag;

     	return $this;
     }

    /**
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags)
    {
    	$this->tags = $tags;

    	return $this;
    }

    /**
     * @return Closure|string|object
     */
    public function getConcrete()
    {
    	return $this->concrete;
    }

    /**
     * @param Closure|string|object $concrete
     *
     * @return self
     */
    public function setConcrete($concrete)
    {
    	$this->concrete = $concrete;

    	return $this;
    }

    /**
     * @return Object
     */
    public function getResolved()
    {
    	return $this->resolved;
    }

    /**
     * @param Object $resolved
     *
     * @return self
     */
    public function setResolved($resolved)
    {
    	$this->resolved = $resolved;

    	return $this;
    }

    /**
     * @return boolean $bootable
     */
    public function isBootable()
    {
    	return $this->bootable;
    }

    /**
     * @param boolean $bootable $bootable
     *
     * @return self
     */
    public function setBootable($bootable)
    {
    	$this->bootable = $bootable;

    	return $this;
    }


}