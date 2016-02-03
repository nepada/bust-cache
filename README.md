Bust Cache Macro
================

[![Build Status](https://travis-ci.org/nepada/bust-cache.svg?branch=master)](https://travis-ci.org/nepada/bust-cache)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/bust-cache.svg)](https://packagist.org/packages/nepada/bust-cache)
[![Latest stable](https://img.shields.io/packagist/v/nepada/bust-cache.svg)](https://packagist.org/packages/nepada/bust-cache)


Installation
------------

Via Composer:

```sh
$ composer require nepada/bust-cache
```

Register the extension in `config.neon`:

```yaml
extensions:
	- Nepada\Bridges\BustCacheDI\BustCacheExtension(%wwwDir%, %debugMode%)
```

If you're using stand-alone Latte, install the macro manually:
```php
$compiler = $engine->getCompiler();
$compiler->addMacro('bustCache', new Nepada\Bridges\BustCacheLatte\BustCacheMacro($compiler, $wwwDir, $debugMode));
```


Usage
-----

Example:

```latte
<link rel="stylesheet" href="{$basePath}{bustCache /css/style.css}">
```

It is not recommended (but supported) to pass variables into the macro, because they need to be resolved in run-time and thus the file is read on every request.
