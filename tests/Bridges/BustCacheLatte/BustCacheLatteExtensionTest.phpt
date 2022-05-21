<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\BustCacheLatte;

use Latte;
use Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension;
use Nepada\BustCache\BustCachePathProcessor;
use Nepada\BustCache\CacheBustingStrategies\ContentHash;
use Nepada\BustCache\FileSystem\LocalFileSystem;
use NepadaTests\TestCase;
use Nette\Utils\Strings;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class BustCacheLatteExtensionTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/../../fixtures';

    /**
     * @dataProvider provideLatteTagData
     * @param string $latteString
     * @param string $expectedCompiledCode
     */
    public function testLatteTag(string $latteString, string $expectedCompiledCode): void
    {
        $latte = $this->createLatte();
        $actualCode = $this->normalizeCode($latte->compile($latteString));
        Assert::contains($expectedCompiledCode, $actualCode);
    }

    /**
     * @return \Generator<mixed[]>
     */
    protected function provideLatteTagData(): \Generator
    {
        yield 'literal file' => [
            'latteString' => '<script src="{bustCache /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor(\'/test.txt\')) /* line 1 */;',
        ];

        yield 'dynamic file' => [
            'latteString' => '<script src="{bustCache $file}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor($file)) /* line 1 */;',
        ];
    }

    private function createLatte(): Latte\Engine
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $strategy = new ContentHash();
        $bustCachePathProcessor = new BustCachePathProcessor($fileSystem, $strategy);
        $latte = new Latte\Engine();
        $latte->setLoader(new Latte\Loaders\StringLoader());
        $latte->addExtension(new BustCacheLatteExtension($bustCachePathProcessor));
        return $latte;
    }

    private function normalizeCode(string $code): string
    {
        return Strings::replace($code, '~^\s*(echo.*)~m', '$1');
    }

}


(new BustCacheLatteExtensionTest())->run();
