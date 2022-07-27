<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\BustCacheLatte;

use Latte;
use Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension;
use Nepada\BustCache\BustCachePathProcessor;
use Nepada\BustCache\CacheBustingStrategies\ContentHash;
use Nepada\BustCache\Caching\NullCache;
use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nepada\BustCache\Manifest\DefaultRevisionFinder;
use Nepada\BustCache\Manifest\NullManifestFinder;
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
     * @param bool $autoRefresh
     */
    public function testLatteTag(string $latteString, string $expectedCompiledCode, bool $autoRefresh): void
    {
        $latte = $this->createLatte($autoRefresh);
        $actualCode = $this->normalizeCode($latte->compile($latteString));
        Assert::contains($expectedCompiledCode, $actualCode);
    }

    /**
     * @return \Generator<mixed[]>
     */
    protected function provideLatteTagData(): \Generator
    {
        yield 'file literal, enabled auto refresh' => [
            'autoRefresh' => true,
            'latteString' => '<script src="{bustCache /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor->__invoke(\'/test.txt\', true)) /* line 1 */;',
        ];

        yield 'file literal, disabled auto refresh' => [
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr(\'/test.txt?a1d0c6e83f\') /* line 1 */;',
        ];

        yield 'dynamic file literal, disabled auto refresh' => [
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache dynamic /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor->__invoke(\'/test.txt\', true)) /* line 1 */;',
        ];

        yield 'file expression, enabled auto refresh' => [
            'autoRefresh' => true,
            'latteString' => '<script src="{bustCache $file}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor->__invoke($file, true)) /* line 1 */;',
        ];

        yield 'file expression, disabled auto refresh' => [
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache $file}"></script>',
            'expectedCompiledCode' => 'echo LR\Filters::escapeHtmlAttr($this->global->bustCachePathProcessor->__invoke($file, false)) /* line 1 */;',
        ];
    }

    private function createLatte(bool $autoRefresh): Latte\Engine
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $strategy = new ContentHash();
        $revisionFinder = new DefaultRevisionFinder($fileSystem, new NullManifestFinder());
        $bustCachePathProcessor = new BustCachePathProcessor($fileSystem, new NullCache(), $revisionFinder, $strategy);
        $latte = new Latte\Engine();
        $latte->setLoader(new Latte\Loaders\StringLoader());
        $latte->addExtension(new BustCacheLatteExtension($bustCachePathProcessor, $autoRefresh));
        return $latte;
    }

    private function normalizeCode(string $code): string
    {
        return Strings::replace($code, '~^\s*(echo.*)~m', '$1');
    }

}


(new BustCacheLatteExtensionTest())->run();
