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
class ModificationTimeTest extends TestCase
{

    public function testCalculateHashSucceeds(): void
    {
        $strategy = new Nepada\BustCache\CacheBustingStrategies\ModificationTime();
        $file = File::fromLocalPath(__DIR__ . '/../../fixtures/test.txt');
        Assert::match('~^[0-9a-f]{10}$~', $strategy->calculateHash($file));
    }

    public function testUnableToReadFile(): void
    {
        $strategy = new Nepada\BustCache\CacheBustingStrategies\ModificationTime();
        $file = File::fromLocalPath('dummy');
        Assert::exception(
            function () use ($strategy, $file): void {
                $strategy->calculateHash($file);
            },
            Nepada\BustCache\FileSystem\IOException::class,
            "Failed to read modification time of 'dummy'",
        );
    }

}


(new ModificationTimeTest())->run();
