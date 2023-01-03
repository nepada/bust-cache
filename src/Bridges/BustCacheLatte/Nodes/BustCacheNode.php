<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheLatte\Nodes;

use Latte\Compiler\NodeHelpers;
use Latte\Compiler\Nodes\Php\ExpressionNode;
use Latte\Compiler\Nodes\Php\ModifierNode;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Nepada\BustCache\BustCachePathProcessor;
use Nepada\BustCache\FileSystem\FileNotFoundException;
use Nepada\BustCache\FileSystem\IOException;
use Nepada\BustCache\Manifest\InvalidManifestException;

/**
 * {bustCache $file}
 * {bustCache dynamic $file}
 */
final class BustCacheNode extends StatementNode
{

    public ExpressionNode $file;

    private bool $strictMode;

    private bool $autoRefresh;

    private BustCachePathProcessor $bustCachePathProcessor;

    public function __construct(ExpressionNode $file, bool $strictMode, bool $autoRefresh, BustCachePathProcessor $bustCachePathProcessor)
    {
        $this->file = $file;
        $this->strictMode = $strictMode;
        $this->autoRefresh = $autoRefresh;
        $this->bustCachePathProcessor = $bustCachePathProcessor;
    }

    public static function create(Tag $tag, bool $strictMode, bool $autoRefresh, BustCachePathProcessor $bustCachePathProcessor): self
    {
        $tag->outputMode = $tag::OutputKeepIndentation;
        $tag->expectArguments();
        $autoRefresh = $tag->parser->tryConsumeModifier('dynamic') !== null || $autoRefresh;
        $file = $tag->parser->parseUnquotedStringOrExpression();

        return new self($file, $strictMode, $autoRefresh, $bustCachePathProcessor);
    }

    /**
     * @param PrintContext $context
     * @return string
     * @throws IOException
     * @throws InvalidManifestException
     */
    public function print(PrintContext $context): string
    {
        // safeUrl is intentionally disabled, we verify the node content represents a valid file path inside BustCachePathProcessor
        $modifier = new ModifierNode([], true, false);

        if (! $this->autoRefresh) {
            try {
                /** @throws \InvalidArgumentException */
                $fileValue = NodeHelpers::toValue($this->file, constants: true);
                if (! is_string($fileValue)) {
                    throw new \InvalidArgumentException('File path must be a string');
                }
                $processedPath = $this->bustCachePathProcessor->__invoke($fileValue, true, false);
                return $context->format(
                    'echo %modify(%dump) %line;',
                    $modifier,
                    $processedPath,
                    $this->position,
                );
            } catch (\InvalidArgumentException) { // non-literal file argument used => compile time processing not possible
            } catch (FileNotFoundException) { // missing file => compile time not possible
            }
        }

        return $context->format(
            'echo %modify($this->global->bustCachePathProcessor->__invoke(%node, %dump, %dump)) %line;',
            $modifier,
            $this->file,
            $this->strictMode,
            $this->autoRefresh,
            $this->position,
        );
    }

    public function &getIterator(): \Generator
    {
        yield $this->file;
    }

}
