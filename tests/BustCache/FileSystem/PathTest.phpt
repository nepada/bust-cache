<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache\FileSystem;

use Nepada\BustCache\FileSystem\Path;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class PathTest extends TestCase
{

    /**
     * @dataProvider provideJoinData
     * @param array<int, Path|string> $parts
     * @param string $expectedPath
     */
    public function testJoin(array $parts, string $expectedPath): void
    {
        Assert::same($expectedPath, Path::join(...$parts)->toString());
    }

    /**
     * @return \Generator<mixed[]>
     */
    protected function provideJoinData(): \Generator
    {
        yield 'empty path' => [
            'parts' => [],
            'expectedPath' => '',
        ];
        yield 'simple combination of string and Path' => [
            'parts' => ['foo', Path::of('bar')],
            'expectedPath' => 'foo/bar',
        ];
        yield 'parts with slashes' => [
            'parts' => ['/foo/bar/', '/baz', '/buz/'],
            'expectedPath' => '/foo/bar/baz/buz/',
        ];
    }

}


(new PathTest())->run();
