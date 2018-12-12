<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Service;

interface ServiceFactory
{
	public function addWithClass($className);

	public function addWithData(array $data = []);
	
}