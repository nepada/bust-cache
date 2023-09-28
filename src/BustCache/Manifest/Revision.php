<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\Path;

final class Revision
{

    public function __construct(
        public readonly Path $revisionPath,
        public readonly File $revisionFile,
        public readonly ?File $sourceManifestFile,
    )
    {
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getRevisionPath(): Path
    {
        return $this->revisionPath;
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getRevisionFile(): File
    {
        return $this->revisionFile;
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getSourceManifestFile(): ?File
    {
        return $this->sourceManifestFile;
    }

}
