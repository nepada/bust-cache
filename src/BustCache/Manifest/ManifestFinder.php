<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\Path;

interface ManifestFinder
{

    /**
     * @throws FileNotFoundException
     */
    public function find(Path $assetPath): ?Manifest;

}
