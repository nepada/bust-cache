Bust Cache Latte Tag
====================

[![Build Status](https://github.com/nepada/bust-cache/workflows/CI/badge.svg)](https://github.com/nepada/bust-cache/actions?query=workflow%3ACI+branch%3Amaster)
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
    bustCache: Nepada\Bridges\BustCacheDI\BustCacheExtension(%wwwDir%, %debugMode%)

# default config
bustCache:
  strategy: contentHash # modificationTime in debugMode
```

If you're using stand-alone Latte, install the macro manually:

```php
$fileSystem = Nepada\BustCache\LocalFileSystem::forDirectory($wwwDir);
$strategy = new Nepada\BustCache\CacheBustingStrategies\ContentHash(); // or other
$pathProcessor = new Nepada\BustCache\BustCachePathProcessor($fileSystem, $strategy);
$latte->addExtension(new Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension($pathProcessor));
```


Usage
-----

Example:

```latte
<link rel="stylesheet" href="{bustCache /css/style.css}">
```

The resulting path depends on the (auto-)chosen cache busting strategy:

```latte
<!-- modificationTime: timestamp of last file modification -->
<link rel="stylesheet" href="/css/style.css?1449177985">

<!-- contentHash:  first 10 letters of md5 hash of the file content -->
<link rel="stylesheet" href="/css/style.css?a1d0c6e83a">
```
