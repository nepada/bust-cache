<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Nepada\BustCache\Caching\Cache;
use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\FileSystem\Path;
use Nepada\BustCache\Manifest\InvalidManifestException;
use Nepada\BustCache\Manifest\RevisionFinder;

final class BustCachePathProcessor
{

    private FileSystem $fileSystem;

    private Cache $cache;

    private RevisionFinder $revisionFinder;

    private CacheBustingStrategy $cacheBustingStrategy;

    public function __construct(FileSystem $fileSystem, Cache $cache, RevisionFinder $revisionFinder, CacheBustingStrategy $cacheBustingStrategy)
    {
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
        $this->revisionFinder = $revisionFinder;
        $this->cacheBustingStrategy = $cacheBustingStrategy;
    }

    /**
     * @param string $path
     * @param bool $autoRefreshCache
     * @return string
     * @throws IOException
     * @throws InvalidManifestException
     * @throws FileNotFoundException
     */
    public function __invoke(string $path, bool $autoRefreshCache): string
    {
        $assetPath = Path::of($path);
        return $this->loadFromCache($assetPath, $autoRefreshCache)
            ?? $this->bustCacheUsingRevisionManifest($assetPath)
            ?? $this->bustCacheUsingQueryParameter($assetPath);
    }

    private function loadFromCache(Path $assetPath, bool $checkFileDependencies): ?string
    {
        $cachedResult = $this->cache->load($this->getCacheKey($assetPath), $checkFileDependencies);
        if (! is_string($cachedResult)) {
            return null;
        }

        return $cachedResult;
    }

    /**
     * @param Path $assetPath
     * @return string|null
     * @throws IOException
     * @throws InvalidManifestException
     * @throws FileNotFoundException
     */
    private function bustCacheUsingRevisionManifest(Path $assetPath): ?string
    {
        $revision = $this->revisionFinder->find($assetPath);
        if ($revision === null) {
            return null;
        }

        $result = $revision->getRevisionPath()->toString();
        $fileDependencies = [];
        $fileDependencies[] = $revision->getRevisionFile();
        if ($revision->getSourceManifestFile() !== null) {
            $fileDependencies[] = $revision->getSourceManifestFile();
        }
        $this->cache->save($this->getCacheKey($assetPath), $result, $fileDependencies);

        return $result;
    }

    /**
     * @param Path $assetPath
     * @return string
     * @throws FileNotFoundException
     * @throws IOException
     */
    private function bustCacheUsingQueryParameter(Path $assetPath): string
    {
        $file = $this->fileSystem->getFile($assetPath);
        $hash = $this->cacheBustingStrategy->calculateHash($file);
        $result = "{$assetPath->toString()}?{$hash}";
        $this->cache->save($this->getCacheKey($assetPath), $result, [$file]);

        return $result;
    }

    private function getCacheKey(Path $assetPath): string
    {
        return $assetPath->toString();
    }

}
