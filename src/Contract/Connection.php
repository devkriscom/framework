<?php
declare (strict_types = 1);

namespace Nusantara\Contract;

interface Connection {

	public function write($content);

	public function stream($content);
	
}