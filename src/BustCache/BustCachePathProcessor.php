<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\FileSystem\Path;

final class BustCachePathProcessor
{

    private FileSystem $fileSystem;

    private CacheBustingStrategy $cacheBustingStrategy;

    public function __construct(FileSystem $fileSystem, CacheBustingStrategy $cacheBustingStrategy)
    {
        $this->fileSystem = $fileSystem;
        $this->cacheBustingStrategy = $cacheBustingStrategy;
    }

    /**
     * @param string $path
     * @return string
     * @throws FileNotFoundException
     * @throws IOException
     */
    public function __invoke(string $path): string
    {
        $assetPath = Path::of($path);
        $file = $this->fileSystem->getFile($assetPath);
        $hash = $this->cacheBustingStrategy->calculateHash($file);
        return "{$assetPath->toString()}?{$hash}";
    }

}
