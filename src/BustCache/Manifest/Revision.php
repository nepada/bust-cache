<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nepada\BustCache\FileSystem\File;
use Nepada\BustCache\FileSystem\Path;

final class Revision
{

    private Path $revisionPath;

    private File $revisionFile;

    private ?File $sourceManifestFile;

    public function __construct(Path $revisionPath, File $revisionFile, ?File $sourceManifestFile)
    {
        $this->revisionPath = $revisionPath;
        $this->revisionFile = $revisionFile;
        $this->sourceManifestFile = $sourceManifestFile;
    }

    public function getRevisionPath(): Path
    {
        return $this->revisionPath;
    }

    public function getRevisionFile(): File
    {
        return $this->revisionFile;
    }

    public function getSourceManifestFile(): ?File
    {
        return $this->sourceManifestFile;
    }

}
