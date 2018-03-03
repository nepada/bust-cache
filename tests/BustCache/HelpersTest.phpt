<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache;

use Nepada;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class HelpersTest extends TestCase
{

    public function testHash(): void
    {
        Assert::same('a1d0c6e83f', Nepada\BustCache\Helpers::hash(__DIR__ . '/../fixtures/test.txt'));
    }

    public function testErrors(): void
    {
        Assert::exception(
            function (): void {
                Nepada\BustCache\Helpers::timestamp('nonExistent');
            },
            Nepada\BustCache\FileNotFoundException::class,
            'Unable to read file \'nonExistent\' - the file does not exist or is not readable.'
        );

        Assert::exception(
            function (): void {
                Nepada\BustCache\Helpers::hash('nonExistent');
            },
            Nepada\BustCache\FileNotFoundException::class,
            'Unable to read file \'nonExistent\' - the file does not exist or is not readable.'
        );
    }

}


(new HelpersTest())->run();
