<?php

declare (strict_types = 1);

namespace Nusantara\Exception\IO\Filesystem;

use Exceptions\Tag\ExistsTag;

/**
 * Use this exception when your code tries to create a local directory but it already exists.
 *
 * @author   Mathieu Dumoulin <thecrazycodr@gmail.com>
 * @license  MIT
 */
class DirectoryAlreadyExistsException extends FilesystemException implements ExistsTag
{
    const MESSAGE = 'Cannot find specified directory';
    const CODE = 0;
}
