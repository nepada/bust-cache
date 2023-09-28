<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nepada\BustCache\FileSystem\Path;
use Nepada\BustCache\Manifest\StaticManifestFinder;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class StaticManifestFinderTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/fixtures';

    public function testThrowsFileNotFoundWhenManifestDoesNotExist(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = StaticManifestFinder::forFilePath('does-not-exist.json', $fileSystem);
        Assert::exception(
            fn () => $finder->find(Path::of('does-not-exist')),
            FileNotFoundException::class,
        );
    }

    public function testGetStaticManifest(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = StaticManifestFinder::forFilePath('manifest.json', $fileSystem);
        $manifest = $finder->find(Path::of('/foo/bar/baz'));
        Assert::same(realpath(__DIR__ . '/fixtures/manifest.json'), realpath($manifest->manifestFile->path->toString()));
        Assert::same('/', $manifest->basePath->toString());
    }

    public function testGetStaticManifestWithNonTrivialBasePath(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = StaticManifestFinder::forFilePath('assets/manifest.json', $fileSystem);
        $manifest = $finder->find(Path::of('/foo/bar/baz'));
        Assert::same(realpath(__DIR__ . '/fixtures/assets/manifest.json'), realpath($manifest->manifestFile->path->toString()));
        Assert::same('/assets', $manifest->basePath->toString());
    }

}


(new StaticManifestFinderTest())->run();
