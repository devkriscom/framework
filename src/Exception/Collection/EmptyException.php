<?php

declare (strict_types = 1);

namespace Nusantara\Exception\Collection;

/**
 * Use this exception when an operation on a collection cannot be completed because the collection is empty.
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
class EmptyException extends CollectionException
{
    const MESSAGE = 'Array/collection is currently empty';
    const CODE = 0;
}
