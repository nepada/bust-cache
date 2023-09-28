<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\Manifest;

use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nepada\BustCache\FileSystem\Path;
use Nepada\BustCache\Manifest\DefaultRevisionFinder;
use Nepada\BustCache\Manifest\InvalidManifestException;
use Nepada\BustCache\Manifest\StaticManifestFinder;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class DefaultRevisionFinderTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/fixtures';

    public function testManifestWithInvalidJsonFails(): void
    {
        $finder = $this->createDefaultRevisionFinder('invalid-json.json');
        Assert::exception(
            fn () => $finder->find(Path::of('foo')),
            InvalidManifestException::class,
            "Manifest file '%a%/invalid-json.json' does not contain a valid json",
        );
    }

    public function testManifestWithUnexpectedContentFails(): void
    {
        $finder = $this->createDefaultRevisionFinder('unexpected-content.json');
        Assert::exception(
            fn () => $finder->find(Path::of('foo')),
            InvalidManifestException::class,
            "Manifest file '%a%/unexpected-content.json' does not contain an expected shape for revision map",
        );
    }

    public function testRevisionFileNotFound(): void
    {
        $finder = $this->createDefaultRevisionFinder('manifest.json');
        Assert::exception(
            fn () => $finder->find(Path::of('asset-revision-is-missing.js')),
            FileNotFoundException::class,
            "File '%a%/not-found.json' does not exist",
        );
    }

    /**
     * @dataProvider provideRevisionData
     */
    public function testFindRevision(string $manifestFilePath, string $assetPath, string $expectedRevisionPath): void
    {
        $finder = $this->createDefaultRevisionFinder($manifestFilePath);
        $revision = $finder->find(Path::of($assetPath));
        Assert::notNull($revision);
        Assert::same($expectedRevisionPath, $revision->revisionPath->toString());
        Assert::same(self::FIXTURES_DIR . $expectedRevisionPath, $revision->revisionFile->path->toString());
        Assert::same(self::FIXTURES_DIR . "/$manifestFilePath", $revision->sourceManifestFile?->path->toString());
    }

    /**
     * @return \Generator<string, array<mixed>>
     */
    protected function provideRevisionData(): \Generator
    {
        yield 'trivial base path, revision in root' => [
            'manifestFilePath' => 'manifest.json',
            'assetPath' => 'manifest.js',
            'expectedRevisionPath' => '/manifest.json',
        ];
        yield 'trivial base path, asset in subdir' => [
            'manifestFilePath' => 'manifest.json',
            'assetPath' => '/assets/manifest.js',
            'expectedRevisionPath' => '/assets/manifest.json',
        ];
        yield 'trivial base path, revision in subdir' => [
            'manifestFilePath' => 'manifest.json',
            'assetPath' => '/revision-in-subdir.js',
            'expectedRevisionPath' => '/assets/js/app-rev.js',
        ];
        yield 'non-trivial base path, revision in same dir' => [
            'manifestFilePath' => 'assets/manifest.json',
            'assetPath' => 'assets/manifest.js',
            'expectedRevisionPath' => '/assets/manifest.json',
        ];
        yield 'non-trivial base path, asset in subdir' => [
            'manifestFilePath' => 'assets/manifest.json',
            'assetPath' => '/assets/js/app.js',
            'expectedRevisionPath' => '/assets/js/app-rev.js',
        ];
    }

    /**
     * @dataProvider provideRevisionNotFoundData
     */
    public function testFindRevisionNotFound(string $manifestFilePath, string $assetPath): void
    {
        $finder = $this->createDefaultRevisionFinder($manifestFilePath);
        $revision = $finder->find(Path::of($assetPath));
        Assert::null($revision);
    }

    /**
     * @return \Generator<string, array<mixed>>
     */
    protected function provideRevisionNotFoundData(): \Generator
    {
        yield 'base path of manifest not compatible with asset' => [
            'manifestFilePath' => 'assets/manifest.json',
            'assetPath' => 'manifest.js',
        ];
        yield 'asset revision not in manifest' => [
            'manifestFilePath' => 'manifest.json',
            'assetPath' => 'foo',
        ];
    }

    private function createDefaultRevisionFinder(string $manifestFilePath): DefaultRevisionFinder
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $manifestFinder = StaticManifestFinder::forFilePath($manifestFilePath, $fileSystem);
        return new DefaultRevisionFinder($fileSystem, $manifestFinder);
    }

}


(new DefaultRevisionFinderTest())->run();
