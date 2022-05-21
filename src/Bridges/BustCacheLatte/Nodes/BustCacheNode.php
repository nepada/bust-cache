<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheLatte\Nodes;

use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;

/**
 * {bustCache $file}
 */
final class BustCacheNode extends StatementNode
{

    public ExpressionNode $file;

    public function __construct(ExpressionNode $file)
    {
        $this->file = $file;
    }

    public static function create(Tag $tag): self
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $file = $tag->parser->parseUnquotedStringOrExpression();

        return new self($file);
    }

    public function print(PrintContext $context): string
    {
        // safeUrl is intentionally disabled, we verify the node content represents a valid file path inside BustCachePathProcessor
        $modifier = new ModifierNode([], true, false);

        return $context->format(
            'echo %modify($this->global->bustCachePathProcessor(%node)) %line;',
            $modifier,
            $this->file,
            $this->position,
        );
    }

    public function &getIterator(): \Generator
    {
        yield $this->file;
    }

}
