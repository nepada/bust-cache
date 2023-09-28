<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

use Nepada\BustCache\FileSystem\File;

interface Cache
{

    /**
     * @param File[] $fileDependencies
     */
    public function save(string $key, mixed $value, array $fileDependencies = []): void;

    public function load(string $key, bool $checkFileDependencies): mixed;

}
