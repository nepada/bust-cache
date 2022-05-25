<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheLatte;

use Latte;
use Latte\Compiler\Tag;
use Latte\Engine;
use Nepada\Bridges\BustCacheLatte\Nodes\BustCacheNode;
use Nepada\BustCache\BustCachePathProcessor;

final class BustCacheLatteExtension extends Latte\Extension
{

    private BustCachePathProcessor $bustCachePathProcessor;

    private bool $autoRefresh;

    public function __construct(BustCachePathProcessor $bustCachePathProcessor, bool $autoRefresh)
    {
        $this->bustCachePathProcessor = $bustCachePathProcessor;
        $this->autoRefresh = $autoRefresh;
    }

    /**
     * @return array<string, callable(Latte\Compiler\Tag, Latte\Compiler\TemplateParser): (Latte\Compiler\Node|\Generator|void)|\stdClass>
     */
    public function getTags(): array
    {
        return [
            'bustCache' => fn (Tag $tag): BustCacheNode => Nodes\BustCacheNode::create($tag, $this->autoRefresh, $this->bustCachePathProcessor),
        ];
    }

    /**
     * @return array<mixed>
     */
    public function getProviders(): array
    {
        return [
            'bustCachePathProcessor' => $this->bustCachePathProcessor,
        ];
    }

    public function getCacheKey(Engine $engine): mixed
    {
        return [
            'autoRefresh' => $this->autoRefresh,
        ];
    }

}
