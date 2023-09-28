<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\IOException;

interface CacheBustingStrategy
{

    /**
     * @throws IOException
     */
    public function calculateHash(File $file): string;

}
