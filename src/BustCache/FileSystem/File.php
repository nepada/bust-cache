<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class File
{

    private Path $path;

    private function __construct(Path $path)
    {
        $this->path = $path;
    }

    public static function fromLocalPath(Path|string $path): static
    {
        if (is_string($path)) {
            $path = Path::of($path);
        }
        return new static($path);
    }

    public function getPath(): Path
    {
        return $this->path;
    }

}
