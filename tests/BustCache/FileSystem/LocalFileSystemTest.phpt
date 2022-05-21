<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\FileSystem;

use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nepada\BustCache\FileSystem\Path;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class LocalFileSystemTest extends TestCase
{

    /**
     * @dataProvider provideFileExistsData
     * @param string $path
     * @param bool $expectExists
     */
    public function testFileExists(string $path, bool $expectExists): void
    {
        $localFileSystem = LocalFileSystem::forDirectory(__DIR__ . '/../..');
        Assert::same($expectExists, $localFileSystem->fileExists(Path::of($path)));
    }

    /**
     * @return \Generator<mixed[]>
     */
    protected function provideFileExistsData(): \Generator
    {
        yield 'existing file' => [
            'path' => '/bootstrap.php',
            'expectExists' => true,
        ];

        yield 'directory' => [
            'path' => '/BustCache',
            'expectExists' => false,
        ];

        yield 'file outside of base dir' => [
            'path' => '/../composer.json',
            'expectExists' => false,
        ];
    }

}


(new LocalFileSystemTest())->run();
