<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheDI;

use Latte;
use Nepada\BustCache\BustCacheMacro;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;

class BustCacheExtension extends Nette\DI\CompilerExtension
{

    private string $wwwDir;

    private bool $debugMode;

    public function __construct(string $wwwDir, bool $debugMode = false)
    {
        $this->wwwDir = $wwwDir;
        $this->debugMode = $debugMode;
    }

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([]);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        $latteFactory = $container->getDefinitionByType(ILatteFactory::class);
        assert($latteFactory instanceof Nette\DI\Definitions\FactoryDefinition);
        $latteFactory->getResultDefinition()->addSetup(
            '?->onCompile[] = function (' . Latte\Engine::class . ' $engine): void { $engine->addMacro("bustCache", new ' . BustCacheMacro::class . '(?, ?)); }',
            ['@self', $this->wwwDir, $this->debugMode]
        );
    }

}
