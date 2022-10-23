<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\FileSystem\Path;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

final class DefaultRevisionFinder implements RevisionFinder
{

    private FileSystem $fileSystem;

    private ManifestFinder $manifestFileFinder;

    public function __construct(FileSystem $fileSystem, ManifestFinder $manifestFileFinder)
    {
        $this->fileSystem = $fileSystem;
        $this->manifestFileFinder = $manifestFileFinder;
    }

    /**
     * @param Path $assetPath
     * @return Revision|null
     * @throws IOException
     * @throws InvalidManifestException
     * @throws FileNotFoundException
     */
    public function find(Path $assetPath): ?Revision
    {
        $manifest = $this->manifestFileFinder->find($assetPath);
        if ($manifest === null) {
            return null;
        }

        $normalizedAssetPath = Path::join('/', $assetPath->normalize());
        $basePathPrefix = rtrim($manifest->getBasePath()->toString(), '/') . '/';
        if (! str_starts_with($normalizedAssetPath->toString(), $basePathPrefix)) {
            return null;
        }

        $relativeAssetPathString = Strings::substring($normalizedAssetPath->toString(), Strings::length($basePathPrefix));
        $revisions = $this->loadManifest($manifest->getManifestFile());
        foreach ($revisions as $originalPath => $revisionPathString) {
            if (ltrim($originalPath, '/') === $relativeAssetPathString) {
                $revisionPath = Path::join($manifest->getBasePath(), $revisionPathString);
                $revisionFile = $this->fileSystem->getFile($revisionPath);
                return new Revision($revisionPath, $revisionFile, $manifest->getManifestFile());
            }
        }

        return null;
    }

    /**
     * @param File $file
     * @return array<string, string>
     * @throws IOException
     * @throws InvalidManifestException
     */
    private function loadManifest(File $file): array
    {
        $content = @file_get_contents($file->getPath()->toString());
        if ($content === false) {
            throw IOException::failedToReadContents($file->getPath()->toString());
        }

        try {
            /** @var array<string, string> $manifest */
            $manifest = Json::decode($content, Json::FORCE_ARRAY);
        } catch (JsonException $exception) {
            throw InvalidManifestException::invalidJson($file->getPath()->toString(), $exception);
        }

        if (! Validators::is($manifest, 'string[]')) {
            throw InvalidManifestException::unexpectedContent($file->getPath()->toString());
        }

        return $manifest;
    }

}
