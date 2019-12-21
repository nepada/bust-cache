<?php
declare(strict_types = 1);

namespace NepadaTests\BustCache;

use Latte;
use Nepada\BustCache\BustCacheMacro;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class BustCacheMacroTest extends TestCase
{

    private const FIXTURES_DIR = __DIR__ . '/../fixtures';

    public function testProductionMode(): void
    {
        $compiler = new Latte\Compiler();
        $compiler->addMacro('bustCache', new BustCacheMacro(self::FIXTURES_DIR, false));

        $node = $compiler->expandMacro('bustCache', '"/test.txt"');
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode,
        );

        $node = $compiler->expandMacro('bustCache', "'/test.txt'");
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode,
        );

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode,
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(LR\Filters::safeUrl($file . \'?\' . Nepada\BustCache\Helpers::hash(\'' . self::FIXTURES_DIR . '\' . $file))) ?>',
            $node->openingCode,
        );
    }

    public function testDebugMode(): void
    {
        $compiler = new Latte\Compiler();
        $compiler->addMacro('bustCache', new BustCacheMacro(self::FIXTURES_DIR, true));

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(LR\Filters::safeUrl("/test.txt" . \'?\' . Nepada\BustCache\Helpers::timestamp(\'' . self::FIXTURES_DIR . '\' . "/test.txt"))) ?>',
            $node->openingCode,
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->empty);
        Assert::same(
            '<?php echo LR\Filters::escapeHtmlText(LR\Filters::safeUrl($file . \'?\' . Nepada\BustCache\Helpers::timestamp(\'' . self::FIXTURES_DIR . '\' . $file))) ?>',
            $node->openingCode,
        );
    }

    public function testErrors(): void
    {
        $compiler = new Latte\Compiler();
        $compiler->addMacro('bustCache', new BustCacheMacro(self::FIXTURES_DIR, true));

        Assert::exception(
            function () use ($compiler): void {
                // Set HtmlNode
                $rc = new \ReflectionClass($compiler);
                $property = $rc->getProperty('htmlNode');
                $property->setAccessible(true);
                $property->setValue($compiler, new Latte\HtmlNode('div'));

                $compiler->expandMacro('bustCache', 'test', null, Latte\MacroNode::PREFIX_NONE);
            },
            Latte\CompileException::class,
            'Unknown %a?%attribute n:%a?%bustCache',
        );

        Assert::exception(
            function () use ($compiler): void {
                $compiler->expandMacro('bustCache', 'test', '|modify');
            },
            Latte\CompileException::class,
            'Modifiers are not allowed in {bustCache}.',
        );

        Assert::exception(
            function () use ($compiler): void {
                $compiler->expandMacro('bustCache', '');
            },
            Latte\CompileException::class,
            'Missing file name in {bustCache}.',
        );

        Assert::exception(
            function () use ($compiler): void {
                $compiler->expandMacro('bustCache', 'multi, word');
            },
            Latte\CompileException::class,
            'Multiple arguments are not supported in {bustCache}.',
        );
    }

}


(new BustCacheMacroTest())->run();
