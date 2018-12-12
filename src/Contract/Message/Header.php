<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Message;

interface Header
{
	public function set(string $id, $value);

	public function get(string $id, $default = null);
	
}