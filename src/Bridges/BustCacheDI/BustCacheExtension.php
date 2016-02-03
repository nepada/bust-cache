<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Bridges\BustCacheDI;

use Nepada\Bridges\BustCacheLatte\BustCacheMacro;
use Nette;
use Nette\Bridges\ApplicationLatte\ILatteFactory;


class BustCacheExtension extends Nette\DI\CompilerExtension
{

    /** @var array */
    public $defaults = [];

    /** @var string */
    private $wwwDir;

    /** @var bool */
    private $debugMode;


    /**
     * @param string $wwwDir
     * @param bool $debugMode
     */
    public function __construct($wwwDir, $debugMode)
    {
        $this->wwwDir = $wwwDir;
        $this->debugMode = $debugMode;
    }

    public function loadConfiguration()
    {
        $this->validateConfig($this->defaults);
    }

    public function beforeCompile()
    {
        foreach ($this->getContainerBuilder()->findByType(ILatteFactory::class) as $latteFactory) {
            $latteFactory->addSetup(
                '$httpRequest = ?; ?->onCompile[] = function ($engine) use ($httpRequest) { $engine->getCompiler()->addMacro("bustCache", new ' . BustCacheMacro::class . '($engine->getCompiler(), ?, preg_replace(?, \'\', rtrim($httpRequest->getUrl()->getBaseUrl(), \'/\')), ?)); }',
                ['@\\' . Nette\Http\IRequest::class, '@self', $this->wwwDir, '#https?://[^/]+#A', $this->debugMode]
            );
        }
    }

}
