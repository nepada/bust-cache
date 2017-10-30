<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\BustCache;

use LogicException;
use RuntimeException;


/**
 * Common interface for exceptions
 */
interface Exception extends \Throwable
{

}


/**
 * The exception that is thrown when an I/O error occurs.
 */
class IOException extends RuntimeException implements Exception
{

}


/**
 * The exception that is thrown when accessing a file that does not exist on disk.
 */
class FileNotFoundException extends IOException
{

    /**
     * @param string $file
     * @return FileNotFoundException
     */
    public static function fromFile(string $file): self
    {
        return new static("Unable to read file '$file' - the file does not exist or is not readable.");
    }

}


/**
 * The exception that is thrown when part of a file or directory cannot be found.
 */
class DirectoryNotFoundException extends IOException
{

    /**
     * @param string $directory
     * @return DirectoryNotFoundException
     */
    public static function fromDir(string $directory): self
    {
        return new static("Unable to read directory '$directory' - the directory does not exist, or is not readable.");
    }

}


/**
 * The exception that is thrown when static class is instantiated.
 */
class StaticClassException extends LogicException
{

}
