<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

final class CacheItem
{

    public function __construct(
        public readonly mixed $value,
        /** @var FileDependency[] */
        public readonly array $fileDependencies = [],
    )
    {
    }

    /**
     * @deprecated read the property directly instead
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @deprecated read the property directly instead
     * @return FileDependency[]
     */
    public function getFileDependencies(): array
    {
        return $this->fileDependencies;
    }

}
