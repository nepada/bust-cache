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
use function str_replace;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class BustCacheLatteExtensionTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/../../fixtures';

    /**
     * @dataProvider provideLatteTagData
     */
    public function testLatteTag(string $latteString, string $expectedCompiledCode, bool $strictMode, bool $autoRefresh): void
    {
        $latte = $this->createLatte($strictMode, $autoRefresh);
        $actualCode = $this->normalizeCode($latte->compile($latteString));
        Assert::contains($expectedCompiledCode, $actualCode);
    }

    /**
     * @return \Generator<mixed[]>
     */
    protected function provideLatteTagData(): \Generator
    {
        yield 'file literal, enabled auto refresh' => [
            'strictMode' => false,
            'autoRefresh' => true,
            'latteString' => '<script src="{bustCache /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr($this->global->bustCachePathProcessor->__invoke(\'/test.txt\', false, true)) /* line 1 */;',
        ];

        yield 'file literal, disabled auto refresh' => [
            'strictMode' => false,
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr(\'/test.txt?a1d0c6e83f\') /* line 1 */;',
        ];

        yield 'dynamic file literal, disabled auto refresh' => [
            'strictMode' => false,
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache dynamic /test.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr($this->global->bustCachePathProcessor->__invoke(\'/test.txt\', false, true)) /* line 1 */;',
        ];

        yield 'file expression, enabled auto refresh' => [
            'strictMode' => false,
            'autoRefresh' => true,
            'latteString' => '<script src="{bustCache $file}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr($this->global->bustCachePathProcessor->__invoke($file, false, true)) /* line 1 */;',
        ];

        yield 'file expression, disabled auto refresh' => [
            'strictMode' => false,
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache $file}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr($this->global->bustCachePathProcessor->__invoke($file, false, false)) /* line 1 */;',
        ];

        yield 'file expression, disabled auto refresh, enabled strict mode with missing file' => [
            'strictMode' => true,
            'autoRefresh' => false,
            'latteString' => '<script src="{bustCache does-not-exist.txt}"></script>',
            'expectedCompiledCode' => 'echo LR\HtmlHelpers::escapeAttr($this->global->bustCachePathProcessor->__invoke(\'does-not-exist.txt\', true, false)) /* line 1 */;',
        ];
    }

    private function createLatte(bool $strictMode, bool $autoRefresh): Latte\Engine
    {
        $fileSystem = LocalFileSystem::forDirectory(self::FIXTURES_DIR);
        $strategy = new ContentHash();
        $revisionFinder = new DefaultRevisionFinder($fileSystem, new NullManifestFinder());
        $bustCachePathProcessor = new BustCachePathProcessor($fileSystem, new NullCache(), $revisionFinder, $strategy);
        $latte = new Latte\Engine();
        $latte->setLoader(new Latte\Loaders\StringLoader());
        $latte->addExtension(new BustCacheLatteExtension($bustCachePathProcessor, $strictMode, $autoRefresh));
        return $latte;
    }

    private function normalizeCode(string $code): string
    {
        // BC with Latte <3.1
        $code = str_replace(
            [
                'Filters::escapeHtmlAttr',
            ],
            [
                'HtmlHelpers::escapeAttr',
            ],
            $code,
        );
        $code = Strings::replace($code, '~line (\d+):\d+~', 'line $1');

        return Strings::replace($code, '~^\s*(echo.*)~m', '$1');
    }

}


(new BustCacheLatteExtensionTest())->run();
