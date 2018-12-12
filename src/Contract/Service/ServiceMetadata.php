<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Service;

interface ServiceMetadata
{
	public function getName();

	public function getCallback();
	
}