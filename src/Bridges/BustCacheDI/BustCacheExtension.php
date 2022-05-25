<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheDI;

use Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension;
use Nepada\BustCache\BustCachePathProcessor;
use Nepada\BustCache\CacheBustingStrategies\ContentHash;
use Nepada\BustCache\CacheBustingStrategies\ModificationTime;
use Nepada\BustCache\CacheBustingStrategy;
use Nepada\BustCache\Caching\Cache;
use Nepada\BustCache\Caching\NetteCache;
use Nepada\BustCache\Caching\NullCache;
use Nepada\BustCache\FileSystem\FileSystem;
use Nepada\BustCache\FileSystem\LocalFileSystem;
use Nette;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;

/**
 * @property \stdClass $config
 */
class BustCacheExtension extends Nette\DI\CompilerExtension
{

    private const CACHE_BUSTING_STRATEGIES = [
        ContentHash::NAME => ContentHash::class,
        ModificationTime::NAME => ModificationTime::class,
    ];

    private string $wwwDir;

    private bool $debugMode;

    public function __construct(string $wwwDir, bool $debugMode = false)
    {
        $this->wwwDir = $wwwDir;
        $this->debugMode = $debugMode;
    }

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'strategy' => Expect::anyOf(array_keys(self::CACHE_BUSTING_STRATEGIES))
                ->default($this->debugMode ? ModificationTime::NAME : ContentHash::NAME),
            'autoRefresh' => Expect::bool($this->debugMode),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('fileSystem'))
            ->setType(FileSystem::class)
            ->setFactory([LocalFileSystem::class, 'forDirectory'], [$this->wwwDir]);

        $container->addDefinition($this->prefix('cacheBustingStrategy'))
            ->setType(CacheBustingStrategy::class)
            ->setFactory(self::CACHE_BUSTING_STRATEGIES[$this->config->strategy]);

        $container->addDefinition($this->prefix('cache'))
            ->setType(Cache::class)
            ->setFactory(NullCache::class);

        $container->addDefinition($this->prefix('pathProcessor'))
            ->setType(BustCachePathProcessor::class);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        if ($container->getByType(Nette\Caching\Storage::class) !== null) {
            $cache = $container->getDefinitionByType(Cache::class);
            assert($cache instanceof Nette\DI\Definitions\ServiceDefinition);
            $cache->setFactory([NetteCache::class, 'withStorage']);
        }

        $pathProcessor = $container->getDefinitionByType(BustCachePathProcessor::class);
        /** @var LatteExtension $latteExtension */
        foreach ($this->compiler->getExtensions(LatteExtension::class) as $latteExtension) {
            $latteExtension->addExtension(new Statement(BustCacheLatteExtension::class, [$pathProcessor, $this->config->autoRefresh]));
        }
    }

}
