<?php
declare(strict_types = 1);

namespace Nepada\BustCache\CacheBustingStrategies;

use Nepada\BustCache\CacheBustingStrategy;
use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\IOException;

final class ModificationTime implements CacheBustingStrategy
{

    public const NAME = 'modificationTime';

    /**
     * @param File $file
     * @return string
     * @throws IOException
     */
    public function calculateHash(File $file): string
    {
        $timestamp = @filemtime($file->getPath()->toString());
        if ($timestamp === false) {
            throw IOException::failedToReadModificationTime($file->getPath()->toString());
        }

        return (string) $timestamp;
    }

}
