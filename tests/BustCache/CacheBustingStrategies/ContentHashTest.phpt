<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\CacheBustingStrategies;

use Nepada;
use Nepada\BustCache\FileSystem\File;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class ContentHashTest extends TestCase
{

    public function testCalculateHashSucceeds(): void
    {
        $strategy = new Nepada\BustCache\CacheBustingStrategies\ContentHash();
        $file = File::fromLocalPath(__DIR__ . '/../../fixtures/test.txt');
        Assert::same('a1d0c6e83f', $strategy->calculateHash($file));
    }

    public function testUnableToReadFile(): void
    {
        $strategy = new Nepada\BustCache\CacheBustingStrategies\ContentHash();
        $file = File::fromLocalPath('dummy');
        Assert::exception(
            function () use ($strategy, $file): void {
                $strategy->calculateHash($file);
            },
            Nepada\BustCache\FileSystem\IOException::class,
            "Failed to read contents of file 'dummy'",
        );
    }

}


(new ContentHashTest())->run();
