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
     * @throws IOException
     */
    public function calculateHash(File $file): string
    {
        $timestamp = @filemtime($file->path->toString());
        if ($timestamp === false) {
            throw IOException::failedToReadModificationTime($file->path->toString());
        }

        return (string) $timestamp;
    }

}
