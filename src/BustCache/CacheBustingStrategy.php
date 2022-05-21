<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\IOException;

interface CacheBustingStrategy
{

    /**
     * @param File $file
     * @return string
     * @throws IOException
     */
    public function calculateHash(File $file): string;

}
