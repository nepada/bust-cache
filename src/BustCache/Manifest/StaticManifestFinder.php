<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\Path;

final class StaticManifestFinder implements ManifestFinder
{

    private Path $manifestFilePath;

    private FileSystem $fileSystem;

    private function __construct(Path $manifestFilePath, FileSystem $fileSystem)
    {
        $this->manifestFilePath = $manifestFilePath;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param Path|string $manifestFilePath
     * @param FileSystem $fileSystem
     * @return static
     */
    public static function forFilePath(Path|string $manifestFilePath, FileSystem $fileSystem): self
    {
        if (is_string($manifestFilePath)) {
            $manifestFilePath = Path::of($manifestFilePath);
        }
        return new self($manifestFilePath, $fileSystem);
    }

    /**
     * @param Path $assetPath
     * @return Manifest
     * @throws FileNotFoundException
     */
    public function find(Path $assetPath): Manifest
    {
        return Manifest::create(
            $this->fileSystem->getFile($this->manifestFilePath),
            $this->manifestFilePath->getDirectoryPath(),
        );
    }

}
