<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\Path;

final class NullManifestFinder implements ManifestFinder
{

    public function find(Path $assetPath): ?Manifest
    {
        return null;
    }

}
