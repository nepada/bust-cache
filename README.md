Bust Cache Macro
================

[![Build Status](https://travis-ci.org/nepada/bust-cache.svg?branch=master)](https://travis-ci.org/nepada/bust-cache)
[![Coverage Status](https://coveralls.io/repos/github/nepada/bust-cache/badge.svg?branch=master)](https://coveralls.io/github/nepada/bust-cache?branch=master)
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
$compiler->addMacro('bustCache', new Nepada\BustCache\BustCacheMacro($compiler, $wwwDir, $debugMode));
```


Usage
-----

Example:

```latte
<link rel="stylesheet" href="{bustCache /css/style.css}">
```

In debug mode the macro busts cache by appending timestamp of last file modification:

```latte
<link rel="stylesheet" href="/css/style.css?1449177985">
```

In production mode the macro busts cache by appending first 10 letters of md5 hash of the file content:

```latte
<link rel="stylesheet" href="/css/style.css?a1d0c6e83f">
```


**Note:** It is not recommended (but supported) to pass variables into the macro, because they need to be resolved in run-time and thus the file is read on every request.
