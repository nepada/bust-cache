<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\BustCacheDI;

use Latte;
use Nepada\BustCache\CacheBustingStrategies\ContentHash;
use Nepada\BustCache\CacheBustingStrategies\ModificationTime;
use Nepada\BustCache\CacheBustingStrategy;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class BustCacheExtensionTest extends TestCase
{

    public function testDebugMode(): void
    {
        $container = $this->createContainer(true);
        Assert::type(ModificationTime::class, $container->getByType(CacheBustingStrategy::class));
        $this->assertCanRenderBustCacheTag($container);
    }

    public function testProductionMode(): void
    {
        $container = $this->createContainer(false);
        Assert::type(ContentHash::class, $container->getByType(CacheBustingStrategy::class));
        $this->assertCanRenderBustCacheTag($container);
    }

    private function createContainer(bool $debugMode): Nette\DI\Container
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode($debugMode);
        $configurator->addParameters(['wwwDir' => __DIR__ . '/../../fixtures']);
        $configurator->addConfig(__DIR__ . '/../../fixtures/config.neon');
        return $configurator->createContainer();
    }

    private function assertCanRenderBustCacheTag(Nette\DI\Container $container): void
    {
        $latte = $container->getByType(LatteFactory::class)->create();
        $latte->setLoader(new Latte\Loaders\StringLoader());
        Assert::noError(
            function () use ($latte): void {
                $latte->compile('{bustCache test.txt}');
            },
        );
    }

}


(new BustCacheExtensionTest())->run();
