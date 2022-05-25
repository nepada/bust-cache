<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

use Nepada\BustCache\FileSystem\File;

final class FileDependency
{

    private string $path;

    private int|false $modificationTime;

    public function __construct(string $path, int|false $mtime)
    {
        $this->path = $path;
        $this->modificationTime = $mtime;
    }

    public static function fromFile(File $file): self
    {
        $stringPath = $file->getPath()->toString();
        return new self($stringPath, @filemtime($stringPath));
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getModificationTime(): int|false
    {
        return $this->modificationTime;
    }

}
