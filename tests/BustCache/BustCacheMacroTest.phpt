<?php
/**
 * Test: Nepada\BustCache\BustCacheMacro
 *
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\BustCache;

use Nepada\BustCache\BustCacheMacro;
use Nette;
use Latte;
use Tester\Assert;
use Tester\TestCase;


require __DIR__ . '/../bootstrap.php';


class BustCacheMacroTest extends TestCase
{

    const FIXTURES_DIR = __DIR__ . '/../fixtures';


    public function testProductionMode()
    {
        $compiler = new Latte\Compiler;
        $compiler->addMacro('bustCache', new BustCacheMacro($compiler, self::FIXTURES_DIR, false));

        $node = $compiler->expandMacro('bustCache', '"/test.txt"');
        Assert::true($node->isEmpty);
        Assert::match(
            '<?php echo %a%->escape%a%\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', "'/test.txt'");
        Assert::true($node->isEmpty);
        Assert::match(
            '<?php echo %a%->escape%a%\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->isEmpty);
        Assert::match(
            '<?php echo %a%->escape%a%\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->isEmpty);
        Assert::match(
            '#<\?php echo .*->escape.*safeUrl.*\$file \. \'\?\' \. Nepada\\\\BustCache\\\\Helpers::hash\(\'' . preg_quote(self::FIXTURES_DIR, '#') . '\' . \$file\)\)\) \?>#i',
            $node->openingCode
        );
    }

    public function testDebugMode()
    {
        $compiler = new Latte\Compiler;
        $compiler->addMacro('bustCache', new BustCacheMacro($compiler, self::FIXTURES_DIR, true));

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->isEmpty);
        Assert::match(
            '#<\?php echo .*->escape.*safeUrl.*"/test\.txt" \. \'\?\' \. Nepada\\\\BustCache\\\\Helpers::timestamp\(\'' . preg_quote(self::FIXTURES_DIR, '#') . '\' . "/test\.txt"\)\)\) \?>#i',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->isEmpty);
        Assert::match(
            '#<\?php echo .*->escape.*safeUrl.*\$file \. \'\?\' \. Nepada\\\\BustCache\\\\Helpers::timestamp\(\'' . preg_quote(self::FIXTURES_DIR, '#') . '\' . \$file\)\)\) \?>#i',
            $node->openingCode
        );
    }

    public function testErrors()
    {
        $compiler = new Latte\Compiler;
        $compiler->addMacro('bustCache', new BustCacheMacro($compiler, self::FIXTURES_DIR, true));

        Assert::exception(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', 'test', null, Latte\MacroNode::PREFIX_NONE);
            },
            Latte\CompileException::class,
            'Unknown %a?%attribute n:%a?%bustCache'
        );

        Assert::exception(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', 'test', '|modify');
            },
            Latte\CompileException::class,
            'Modifiers are not allowed in {bustCache}.'
        );

        Assert::exception(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', '');
            },
            Latte\CompileException::class,
            'Missing file name in {bustCache}.'
        );

        Assert::exception(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', 'multi, word');
            },
            Latte\CompileException::class,
            'Multiple arguments are not supported in {bustCache}.'
        );
    }

}


\run(new BustCacheMacroTest());
