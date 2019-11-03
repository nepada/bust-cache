<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheDI;

use Latte;
use Nepada\BustCache\BustCacheMacro;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;

class BustCacheExtension extends Nette\DI\CompilerExtension
{

    /** @var string */
    private $wwwDir;

    /** @var bool */
    private $debugMode;

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

        /** @var Nette\DI\Definitions\FactoryDefinition $latteFactory */
        $latteFactory = $container->getDefinitionByType(ILatteFactory::class);
        $latteFactory->getResultDefinition()->addSetup(
            '?->onCompile[] = function (' . Latte\Engine::class . ' $engine): void { $engine->addMacro("bustCache", new ' . BustCacheMacro::class . '(?, ?)); }',
            ['@self', $this->wwwDir, $this->debugMode]
        );
    }

}
