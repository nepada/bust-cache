<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheLatte;

use Latte;
use Latte\Compiler\Tag;
use Nepada\Bridges\BustCacheLatte\Nodes\BustCacheNode;
use Nepada\BustCache\BustCachePathProcessor;

final class BustCacheLatteExtension extends Latte\Extension
{

    private BustCachePathProcessor $bustCachePathProcessor;

    public function __construct(BustCachePathProcessor $bustCachePathProcessor)
    {
        $this->bustCachePathProcessor = $bustCachePathProcessor;
    }

    /**
     * @return array<string, callable(Latte\Compiler\Tag, Latte\Compiler\TemplateParser): (Latte\Compiler\Node|\Generator|void)|\stdClass>
     */
    public function getTags(): array
    {
        return [
            'bustCache' => fn (Tag $tag): BustCacheNode => Nodes\BustCacheNode::create($tag),
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

}
