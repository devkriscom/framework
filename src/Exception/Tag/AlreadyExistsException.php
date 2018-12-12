<?php

declare (strict_types = 1);

namespace Nusantara\Exception\Tag;

/**
 * This is a tag interface that is used to conveys a shared means throughout many different exceptions in many
 * different declare (strict_types = 1);

namespaces. If you want to catch a potential error about something that already exists, you would try to
 * catch any exception that implements this interface.
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
interface AlreadyExistsException
{
}
