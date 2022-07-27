<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\Path;

final class AutodetectManifestFinder implements ManifestFinder
{

    public const DEFAULT_MANIFEST_FILE_NAMES = [
        'manifest.json',
        'rev-manifest.json',
    ];

    private FileSystem $fileSystem;

    /**
     * @var string[]
     */
    private array $manifestFileNames;

    /**
     * @param FileSystem $fileSystem
     * @param string[] $manifestFileNames
     */
    public function __construct(FileSystem $fileSystem, array $manifestFileNames = self::DEFAULT_MANIFEST_FILE_NAMES)
    {
        $this->fileSystem = $fileSystem;
        $this->manifestFileNames = $manifestFileNames;
    }

    public function find(Path $assetPath): ?Manifest
    {
        $basePath = Path::join('/', $assetPath->normalize())->getDirectoryPath();
        while (true) {
            foreach ($this->manifestFileNames as $manifestFileName) {
                $manifestPath = Path::join($basePath, $manifestFileName);
                try {
                    $manifestFile = $this->fileSystem->getFile($manifestPath);
                    return Manifest::create($manifestFile, $basePath);
                } catch (FileNotFoundException $e) {
                    // continue
                }
            }
            if ($basePath->toString() === '/') {
                break;
            }
            $basePath = $basePath->getDirectoryPath();
        }

        return null;
    }

}
