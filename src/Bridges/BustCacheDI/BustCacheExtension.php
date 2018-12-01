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

    public function loadConfiguration(): void
    {
        $this->validateConfig([]);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        $latteFactory = $container->getDefinitionByType(ILatteFactory::class);
        if (method_exists($latteFactory, 'getResultDefinition')) { // BC with Nette 2.4
            $latteFactory = $latteFactory->getResultDefinition();
        }
        $latteFactory->addSetup(
            '?->onCompile[] = function (' . Latte\Engine::class . ' $engine): void { $engine->addMacro("bustCache", new ' . BustCacheMacro::class . '(?, ?)); }',
            ['@self', $this->wwwDir, $this->debugMode]
        );
    }

}
