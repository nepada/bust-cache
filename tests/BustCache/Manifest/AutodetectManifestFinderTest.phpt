<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\Manifest;

use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nepada\BustCache\FileSystem\Path;
use Nepada\BustCache\Manifest\AutodetectManifestFinder;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class AutodetectManifestFinderTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/fixtures';

    public function testDefaultManifestFileNamePreference(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = new AutodetectManifestFinder($fileSystem);
        $manifest = $finder->find(Path::of('test.txt'));
        Assert::notNull($manifest);
        Assert::same(self::FIXTURES_DIR . '/manifest.json', $manifest->manifestFile->path->toString());
    }

    public function testReversedManifestFileNamePreference(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = new AutodetectManifestFinder($fileSystem, array_reverse(AutodetectManifestFinder::DEFAULT_MANIFEST_FILE_NAMES));
        $manifest = $finder->find(Path::of('test.txt'));
        Assert::notNull($manifest);
        Assert::same(self::FIXTURES_DIR . '/rev-manifest.json', $manifest->manifestFile->path->toString());
    }

    public function testManifestNotFound(): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = new AutodetectManifestFinder($fileSystem, ['does-not-exist.json']);
        $manifest = $finder->find(Path::of('test.txt'));
        Assert::null($manifest);
    }

    /**
     * @dataProvider provideManifestData
     */
    public function testFindManifest(string $assetPath, string $expectedManifestFile, string $expectedBasePath): void
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $finder = new AutodetectManifestFinder($fileSystem);
        $manifest = $finder->find(Path::of($assetPath));
        Assert::notNull($manifest);
        Assert::same($expectedManifestFile, $manifest->manifestFile->path->toString());
        Assert::same($expectedBasePath, $manifest->basePath->toString());
    }

    /**
     * @return \Generator<array<mixed>>
     */
    protected function provideManifestData(): \Generator
    {
        yield 'asset in root => manifest in root' => [
            'app.js',
            self::FIXTURES_DIR . '/manifest.json',
            '/',
        ];
        yield 'asset in subdir without manifest => manifest in root' => [
            '/admin/css/app.js',
            self::FIXTURES_DIR . '/manifest.json',
            '/',
        ];
        yield 'asset in subdir with manifest => manifest in subdir' => [
            '/assets/css/app.js',
            self::FIXTURES_DIR . '/assets/manifest.json',
            '/assets',
        ];
    }

}


(new AutodetectManifestFinderTest())->run();
