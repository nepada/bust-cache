<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\Caching;

use Nepada;
use Nepada\BustCache\FileSystem\File;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class NetteCacheTest extends TestCase
{

    private const KEY = 'key';
    private const VALUE = true;

    public function testCachingWhenFileDependencyCreated(): void
    {
        $cache = $this->createNetteCache();
        $fileDependency = $this->getFileDependency();

        $cache->save(self::KEY, self::VALUE, [$fileDependency]);
        Assert::same(self::VALUE, $cache->load(self::KEY, true), 'read value from cache after saving');

        touch($fileDependency->getPath()->toString());
        Assert::same(null, $cache->load(self::KEY, true), 'read value from cache with checking dependencies');
        Assert::same(self::VALUE, $cache->load(self::KEY, false), 'read value from cache without checking dependencies');
    }

    public function testCachingWhenFileDependencyDeleted(): void
    {
        $cache = $this->createNetteCache();
        $fileDependency = $this->getFileDependency();

        touch($fileDependency->getPath()->toString());
        $cache->save(self::KEY, self::VALUE, [$fileDependency]);
        Assert::same(self::VALUE, $cache->load(self::KEY, true), 'read value from cache after saving');

        unlink($fileDependency->getPath()->toString());
        Assert::same(null, $cache->load(self::KEY, true), 'read value from cache with checking dependencies');
        Assert::same(self::VALUE, $cache->load(self::KEY, false), 'read value from cache without checking dependencies');
    }

    public function testCachingWhenFileDependencyModified(): void
    {
        $cache = $this->createNetteCache();
        $fileDependency = $this->getFileDependency();

        file_put_contents($fileDependency->getPath()->toString(), 'original');
        sleep(1);
        $cache->save(self::KEY, self::VALUE, [$fileDependency]);
        Assert::same(self::VALUE, $cache->load(self::KEY, true), 'read value from cache after saving');

        file_put_contents($fileDependency->getPath()->toString(), 'new');
        clearstatcache(true, $fileDependency->getPath()->toString());
        Assert::same(null, $cache->load(self::KEY, true), 'read value from cache with checking dependencies');
        Assert::same(self::VALUE, $cache->load(self::KEY, false), 'read value from cache without checking dependencies');
    }

    private function getFileDependency(): File
    {
        $path = Environment::getTempDir() . '/dependency';
        return File::fromLocalPath($path);
    }

    private function createNetteCache(): Nepada\BustCache\Caching\NetteCache
    {
        $cacheDirectory = Environment::getTempDir() . '/cache';
        FileSystem::createDir($cacheDirectory);
        return Nepada\BustCache\Caching\NetteCache::withStorage(new FileStorage($cacheDirectory));
    }

}


(new NetteCacheTest())->run();
