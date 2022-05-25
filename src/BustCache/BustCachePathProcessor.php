<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Nepada\BustCache\Caching\Cache;
use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\FileSystem\Path;

final class BustCachePathProcessor
{

    private FileSystem $fileSystem;

    private Cache $cache;

    private CacheBustingStrategy $cacheBustingStrategy;

    public function __construct(FileSystem $fileSystem, Cache $cache, CacheBustingStrategy $cacheBustingStrategy)
    {
        $this->fileSystem = $fileSystem;
        $this->cache = $cache;
        $this->cacheBustingStrategy = $cacheBustingStrategy;
    }

    /**
     * @param string $path
     * @param bool $autoRefreshCache
     * @return string
     * @throws FileNotFoundException
     * @throws IOException
     */
    public function __invoke(string $path, bool $autoRefreshCache): string
    {
        $assetPath = Path::of($path);
        return $this->loadFromCache($assetPath, $autoRefreshCache)
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
