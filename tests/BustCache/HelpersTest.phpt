<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\BustCache;

use Nepada;
use Nette;
use Latte;
use Tester\Assert;
use Tester\TestCase;


require __DIR__ . '/../bootstrap.php';


class HelpersTest extends TestCase
{

    public function testHash()
    {
        Assert::same('a1d0c6e83f', Nepada\BustCache\Helpers::hash(__DIR__ . '/../fixtures/test.txt'));
    }

    public function testErrors()
    {
        Assert::exception(
            function () {
                Nepada\BustCache\Helpers::timestamp('nonExistent');
            },
            Nepada\BustCache\FileNotFoundException::class,
            'Unable to read file \'nonExistent\' - the file does not exist or is not readable.'
        );

        Assert::exception(
            function () {
                Nepada\BustCache\Helpers::hash('nonExistent');
            },
            Nepada\BustCache\FileNotFoundException::class,
            'Unable to read file \'nonExistent\' - the file does not exist or is not readable.'
        );
    }

}


\run(new HelpersTest());
