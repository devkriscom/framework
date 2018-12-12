<?php

declare (strict_types = 1);

namespace Nusantara\Exception\IO\Filesystem;

use Exceptions\Tag\AbortedTag;

/**
 * Use this exception when your code realizes that there is no more space available on the device to write to.
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
class NoMoreSpaceException extends FilesystemException implements AbortedTag
{
    const MESSAGE = 'Specified target location has run out of disk space';
    const CODE = 0;
}
