<?php
declare (strict_types = 1);

namespace Nusantara\Component\Collection;

class ArrayCollection
{
	protected $collections = array();

	public function serialize()
    {
        return \serialize(array(
          
        ));
    }

    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);
    }

	public function all()
	{
		return $this->collections;
	}

	public function has($name)
	{
		return array_key_exists($name, $this->collections);
	}

	public function get($name, $default = null)
	{
		return array_key_exists($name, $this->collections) ? $this->collections[$name] : $default;
	}

	public function set($name, $value)
	{
		if($this->has($name))
		{
			throw new \Exception(sprintf("%s already exist on collection: %s", $name, get_class($this)));
			
		}
		$this->collections[$name] = $value;
	}

	public function replace(array $collections)
	{
		$this->collections = array();
		foreach ($collections as $key => $value) {
			$this->collections[$name] = $value;
		}
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->collections);
	}

	public function count()
	{
		return \count($this->collections);
	}
	
}