<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

interface FileSystem
{

    public function fileExists(Path $path): bool;

    /**
     * @throws FileNotFoundException
     */
    public function getFile(Path $path): File;

}
