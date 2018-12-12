<?php
declare (strict_types = 1);

namespace Nusantara\Contract;

interface Storage
{
	public function disk(string $diskName);
}