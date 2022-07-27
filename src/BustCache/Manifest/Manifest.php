<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\Path;

final class Manifest
{

    private File $manifestFile;

    private Path $basePath;

    private function __construct(File $manifestFile, Path $basePath)
    {
        $this->manifestFile = $manifestFile;
        $this->basePath = $basePath;
    }

    public static function create(File $manifestFile, Path $basePath): static
    {
        return new static(
            $manifestFile,
            Path::join('/', $basePath->normalize()),
        );
    }

    public function getManifestFile(): File
    {
        return $this->manifestFile;
    }

    public function getBasePath(): Path
    {
        return $this->basePath;
    }

}
