<?php
declare(strict_types = 1);

namespace Nepada\BustCache\CacheBustingStrategies;

use Nepada\BustCache\CacheBustingStrategy;
use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\IOException;

final class ContentHash implements CacheBustingStrategy
{

    public const NAME = 'contentHash';

    private const LENGTH = 10;

    /**
     * @throws IOException
     */
    public function calculateHash(File $file): string
    {
        $content = @file_get_contents($file->getPath()->toString());
        if ($content === false) {
            throw IOException::failedToReadContents($file->getPath()->toString());
        }

        return substr(md5($content), 0, self::LENGTH);
    }

}
