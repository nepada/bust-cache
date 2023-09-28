<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

final class CacheItem
{

    private mixed $value;

    /**
     * @var FileDependency[]
     */
    private array $fileDependencies;

    /**
     * @param FileDependency[] $fileDependencies
     */
    public function __construct(mixed $value, array $fileDependencies = [])
    {
        $this->value = $value;
        $this->fileDependencies = $fileDependencies;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return FileDependency[]
     */
    public function getFileDependencies(): array
    {
        return $this->fileDependencies;
    }

}
