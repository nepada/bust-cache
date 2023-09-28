<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\Path;

final class Manifest
{

    private function __construct(
        public readonly File $manifestFile,
        public readonly Path $basePath,
    )
    {
    }

    public static function create(File $manifestFile, Path $basePath): static
    {
        return new static(
            $manifestFile,
            Path::join('/', $basePath->normalize()),
        );
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getManifestFile(): File
    {
        return $this->manifestFile;
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getBasePath(): Path
    {
        return $this->basePath;
    }

}
