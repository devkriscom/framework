<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Service;

use Nusantara\Contract\Message;

interface ServiceManager
{
	public function getMetadata() : array;

	public function addWithClass($className);

	public function addWithData(array $data, array $context = []);

	public function registerInstance(string $callback, $concrete);

	public function terminal(string $name);

} 