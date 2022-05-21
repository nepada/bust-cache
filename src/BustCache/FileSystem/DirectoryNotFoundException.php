<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class DirectoryNotFoundException extends \RuntimeException
{

    public static function at(string $path): self
    {
        return new self("Directory '{$path}' does not exist");
    }

}
