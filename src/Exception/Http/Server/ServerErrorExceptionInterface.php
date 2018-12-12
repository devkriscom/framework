<?php

declare (strict_types = 1);

namespace Nusantara\Exception\Http\Server;

use Exceptions\Http\HttpExceptionInterface;

/**
 * This is a tag interface used to group together all potential Server Error HTTP exceptions (500 class).
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
interface ServerErrorExceptionInterface extends HttpExceptionInterface
{
}
