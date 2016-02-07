<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\BustCache;

use Latte;
use Latte\MacroNode;


/**
 * Macro {bustCache ...}
 */
class BustCacheMacro implements Latte\IMacro
{

    /** @var Latte\Compiler */
    private $compiler;

    /** @var string */
    private $wwwDir;

    /** @var bool */
    private $debugMode;


    /**
     * @param Latte\Compiler $compiler
     * @param string $wwwDir
     * @param bool $debugMode
     */
    public function __construct(Latte\Compiler $compiler, $wwwDir, $debugMode = false)
    {
        $this->compiler = $compiler;
        $this->wwwDir = (string) $wwwDir;
        if (!is_dir($this->wwwDir)) {
            throw DirectoryNotFoundException::fromDir($wwwDir);
        }
        $this->debugMode = (bool) $debugMode;
    }

    public function initialize()
    {
    }

    public function finalize()
    {
    }

    /**
     * New node is found. Returns FALSE to reject.
     *
     * @param MacroNode $node
     * @return bool
     * @throws Latte\CompileException
     */
    public function nodeOpened(MacroNode $node)
    {
        if ($node->prefix) {
            return false;
        }

        if ($node->modifiers) {
            throw new Latte\CompileException("Modifiers are not allowed in {{$node->name}}.");
        }

        $file = $node->tokenizer->fetchWord();
        if ($file === false) {
            throw new Latte\CompileException("Missing file name in {{$node->name}}.");
        } elseif ($node->tokenizer->fetchWord()) {
            throw new Latte\CompileException("Multiple arguments are not supported in {{$node->name}}.");
        }

        $node->isEmpty = true;
        $node->modifiers = '|safeurl|escape'; // auto-escape

        $writer = Latte\PhpWriter::using($node, $this->compiler);

        if ($this->debugMode) {
            $node->openingCode = $writer->write('<?php echo %modify(%1.word . \'?\' . Nepada\BustCache\Helpers::timestamp(%0.var . %1.word)) ?>', $this->wwwDir, $file);

        } elseif (preg_match('#^(["\']?)[^$\'"]*\1$#', $file)) { // Static path
            $file = trim($file, '"\'');
            $url = $file . '?' . Helpers::hash($this->wwwDir . $file);
            $url = Latte\Runtime\Filters::safeUrl($url);
            $node->openingCode = $writer->write('<?php echo %escape(%var) ?>', $url);

        } else {
            $node->openingCode = $writer->write('<?php echo %modify(%1.word . \'?\' . Nepada\BustCache\Helpers::hash(%0.var . %1.word)) ?>', $this->wwwDir, $file);
        }
    }

    /**
     * Node is closed.
     *
     * @param MacroNode $node
     */
    public function nodeClosed(MacroNode $node)
    {
    }

}
