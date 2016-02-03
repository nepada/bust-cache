<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\BustCacheLatte;

use Nepada\Bridges\BustCacheLatte\BustCacheMacro;
use Nette;
use Latte;
use Tester\Assert;
use Tester\TestCase;


require __DIR__ . '/../../bootstrap.php';


class BustCacheMacroTest extends TestCase
{

    const FIXTURES_DIR = __DIR__ . '/../../fixtures';


    public function testProductionMode()
    {
        $compiler = new Latte\Compiler;
        $compiler->addMacro('bustCache', new BustCacheMacro($compiler, self::FIXTURES_DIR, false));

        $node = $compiler->expandMacro('bustCache', '"/test.txt"');
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', "'/test.txt'");
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(\'/test.txt?a1d0c6e83f\') ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(Latte\Runtime\Filters::safeUrl($file . \'?\' . Nepada\BustCache\Helpers::hash(\'' . self::FIXTURES_DIR . '\' . $file))) ?>',
            $node->openingCode
        );
    }

    public function testDebugMode()
    {
        $compiler = new Latte\Compiler;
        $compiler->addMacro('bustCache', new BustCacheMacro($compiler, self::FIXTURES_DIR, true));

        $node = $compiler->expandMacro('bustCache', '/test.txt');
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(Latte\Runtime\Filters::safeUrl("/test.txt" . \'?\' . Nepada\BustCache\Helpers::timestamp(\'' . self::FIXTURES_DIR . '\' . "/test.txt"))) ?>',
            $node->openingCode
        );

        $node = $compiler->expandMacro('bustCache', '$file');
        Assert::true($node->isEmpty);
        Assert::same(
            '<?php echo $template->escape(Latte\Runtime\Filters::safeUrl($file . \'?\' . Nepada\BustCache\Helpers::timestamp(\'' . self::FIXTURES_DIR . '\' . $file))) ?>',
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

        Assert::error(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', 'test', '|modify');
            },
            E_USER_WARNING,
            'Modifiers are not allowed in {bustCache}.'
        );

        Assert::exception(
            function () use ($compiler) {
                $compiler->expandMacro('bustCache', 'multi, word');
            },
            Latte\CompileException::class,
            'BustCache macro does not support multiple arguments.'
        );
    }

}


\run(new BustCacheMacroTest());
