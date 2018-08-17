<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\BustCacheDI;

use Latte;
use NepadaTests\TestCase;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class BustCacheExtensionTest extends TestCase
{

    /** @var Nette\DI\Container */
    private $container;

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addParameters(['wwwDir' => __DIR__ . '/../../fixtures']);
        $configurator->addConfig(__DIR__ . '/../../fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

    public function testContainer(): void
    {
        /** @var Latte\Engine $latte */
        $latte = $this->container->getByType(ILatteFactory::class)->create();
        $latte->setLoader(new Latte\Loaders\StringLoader());
        Assert::noError(
            function () use ($latte): void {
                $latte->compile('{bustCache test}');
            }
        );
    }

}


(new BustCacheExtensionTest())->run();
