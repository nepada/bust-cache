<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Caching;

use Nepada\BustCache\FileSystem\File;
use Nette;

final class NetteCache implements Cache
{

    private Nette\Caching\Cache $netteCache;

    public function __construct(Nette\Caching\Cache $netteCache)
    {
        $this->netteCache = $netteCache;
    }

    public static function withStorage(Nette\Caching\Storage $storage): self
    {
        return new self(new Nette\Caching\Cache($storage, 'nepada.bustCache'));
    }

    /**
     * @param File[] $fileDependencies
     */
    public function save(string $key, mixed $value, array $fileDependencies = []): void
    {
        $item = new CacheItem(
            $value,
            array_map(fn (File $file): FileDependency => FileDependency::fromFile($file), $fileDependencies),
        );
        $this->netteCache->save($key, $item);
    }

    public function load(string $key, bool $checkFileDependencies): mixed
    {
        $item = $this->netteCache->load($key);
        if (! $item instanceof CacheItem) {
            return null;
        }

        if ($checkFileDependencies) {
            foreach ($item->fileDependencies as $fileDependency) {
                if ($fileDependency->modificationTime !== @filemtime($fileDependency->path)) {
                    return null;
                }
            }
        }

        return $item->value;
    }

}
