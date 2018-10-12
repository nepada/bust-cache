<?php
declare(strict_types = 1);

namespace Nepada\Bridges\BustCacheDI;

use Latte;
use Nepada\BustCache\BustCacheMacro;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\DI\MissingServiceException;

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
        try {
            $latteFactory = $container->getDefinitionByType(ILatteFactory::class);
            $latteFactory->addSetup(
                '?->onCompile[] = function (' . Latte\Engine::class . ' $engine): void { $engine->addMacro("bustCache", new ' . BustCacheMacro::class . '(?, ?)); }',
                ['@self', $this->wwwDir, $this->debugMode]
            );
        } catch (MissingServiceException $exception) {
            // noop
        }
    }

}
