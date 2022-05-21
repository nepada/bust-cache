<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class FileNotFoundException extends \RuntimeException
{

    public static function at(string $path): self
    {
        return new self("File '{$path}' does not exist");
    }

    public static function inBaseDirectory(string $path, string $baseDir): self
    {
        return new self("File '{$path}' is outside of allowed base directory '{$baseDir}'");
    }

}
