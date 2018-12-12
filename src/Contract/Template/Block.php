<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Template;

interface Block {

	static public function name() : string;

    static public function metadata() : array;

    public function dataQuery() : array;
	
}