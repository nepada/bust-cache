<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Latte;
use Latte\MacroNode;

/**
 * Macro {bustCache ...}
 */
class BustCacheMacro implements Latte\IMacro
{

    use Latte\Strict;

    /** @var string */
    private $wwwDir;

    /** @var bool */
    private $debugMode;

    /**
     * @param string $wwwDir
     * @param bool $debugMode
     * @throws DirectoryNotFoundException
     */
    public function __construct(string $wwwDir, bool $debugMode = false)
    {
        $this->wwwDir = $wwwDir;
        if (!is_dir($this->wwwDir)) {
            throw new DirectoryNotFoundException($wwwDir);
        }
        $this->debugMode = $debugMode;
    }

    public function initialize(): void
    {
    }

    /**
     * @return mixed[]|null [prolog, epilog]
     */
    public function finalize(): ?array
    {
        return null;
    }

    /**
     * New node is found. Returns FALSE to reject.
     *
     * @param MacroNode $node
     * @return bool
     * @throws IOException
     * @throws Latte\CompileException
     */
    public function nodeOpened(MacroNode $node): bool
    {
        if ($node->prefix !== '' && $node->prefix !== null) {
            return false;
        }

        if ($node->modifiers !== '') {
            throw new Latte\CompileException("Modifiers are not allowed in {{$node->name}}.");
        }

        /** @var string|false|null $file */
        $file = $node->tokenizer->fetchWord();
        if ($file === null || $file === false) { // `false` is BC with Nette 2.4
            throw new Latte\CompileException("Missing file name in {{$node->name}}.");
        }

        /** @var string|false|null $word */
        $word = $node->tokenizer->fetchWord();
        if ($word !== null && $word !== false) { // `false` is BC with Nette 2.4
            throw new Latte\CompileException("Multiple arguments are not supported in {{$node->name}}.");
        }

        $node->empty = true;
        $node->modifiers = '|checkurl|escape'; // auto-escape

        $writer = Latte\PhpWriter::using($node);

        if ($this->debugMode) {
            $node->openingCode = $writer->write('<?php echo %modify(%1.word . \'?\' . Nepada\BustCache\Helpers::timestamp(%0.var . %1.word)) ?>', $this->wwwDir, $file);

        } elseif ((bool) preg_match('#^(["\']?)[^$\'"]*\1$#', $file)) { // Static path
            $file = trim($file, '"\'');
            $url = $file . '?' . Helpers::hash($this->wwwDir . $file);
            $url = Latte\Runtime\Filters::safeUrl($url);
            $node->openingCode = $writer->write('<?php echo %escape(%var) ?>', $url);

        } else {
            $node->openingCode = $writer->write('<?php echo %modify(%1.word . \'?\' . Nepada\BustCache\Helpers::hash(%0.var . %1.word)) ?>', $this->wwwDir, $file);
        }

        return true;
    }

    /**
     * Node is closed.
     *
     * @param MacroNode $node
     */
    public function nodeClosed(MacroNode $node): void
    {
    }

}
