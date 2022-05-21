<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class IOException extends \RuntimeException
{

    public static function create(string $message): self
    {
        return new self($message);
    }

    public static function failedToReadContents(string $filePath): self
    {
        return new self("Failed to read contents of file '{$filePath}'");
    }

    public static function failedToReadModificationTime(string $path): self
    {
        return new self("Failed to read modification time of '{$path}'");
    }

}
