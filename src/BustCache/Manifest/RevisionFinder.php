<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\FileSystem\Path;

interface RevisionFinder
{

    /**
     * @param Path $assetPath
     * @return Revision|null
     * @throws IOException
     * @throws InvalidManifestException
     * @throws FileNotFoundException
     */
    public function find(Path $assetPath): ?Revision;

}
