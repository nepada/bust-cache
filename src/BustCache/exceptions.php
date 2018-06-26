<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

/**
 * The exception that is thrown when an I/O error occurs.
 */
class IOException extends \RuntimeException
{

}


/**
 * The exception that is thrown when accessing a file that does not exist on disk.
 */
class FileNotFoundException extends IOException
{

    public function __construct(string $file, ?\Throwable $previous = null)
    {
        $message = "Unable to read file '$file' - the file does not exist or is not readable.";
        parent::__construct($message, 0, $previous);
    }

}


/**
 * The exception that is thrown when part of a file or directory cannot be found.
 */
class DirectoryNotFoundException extends IOException
{

    public function __construct(string $directory, ?\Throwable $previous = null)
    {
        $message = "Unable to read directory '$directory' - the directory does not exist, or is not readable.";
        parent::__construct($message, 0, $previous);
    }

}


/**
 * The exception that is thrown when static class is instantiated.
 */
class StaticClassException extends \LogicException
{

}
