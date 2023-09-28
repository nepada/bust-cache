<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

use Nepada\BustCache\FileSystem\File;

final class FileDependency
{

    public function __construct(
        public readonly string $path,
        public readonly int|false $modificationTime,
    )
    {
    }

    public static function fromFile(File $file): self
    {
        $stringPath = $file->path->toString();
        return new self($stringPath, @filemtime($stringPath));
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getModificationTime(): int|false
    {
        return $this->modificationTime;
    }

}
