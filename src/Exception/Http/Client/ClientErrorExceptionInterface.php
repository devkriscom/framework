<?php

declare (strict_types = 1);

namespace Nusantara\Exception\Http\Client;

use Exceptions\Http\HttpExceptionInterface;

/**
 * This is a tag interface used to group together all potential Client Error HTTP exceptions (400 class).
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
interface ClientErrorExceptionInterface extends HttpExceptionInterface
{
}
