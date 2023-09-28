<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class File
{

    private function __construct(
        public readonly Path $path,
    )
    {
    }

    public static function fromLocalPath(Path|string $path): static
    {
        if (is_string($path)) {
            $path = Path::of($path);
        }
        return new static($path);
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getPath(): Path
    {
        return $this->path;
    }

}
