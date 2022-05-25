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
```

Overview of available configuration options with their default values:
```yaml
bustCache:
  strategy: contentHash # modificationTime in debugMode
  autoRefresh: %debugMode%
```

If you're using stand-alone Latte, install the Latte extension manually:

```php
$fileSystem = Nepada\BustCache\LocalFileSystem::forDirectory($wwwDir);
$cache = new Nepada\BustCache\Caching\NullCache(); // or other implementation of Cache
$strategy = new Nepada\BustCache\CacheBustingStrategies\ContentHash(); // or other strategy
$pathProcessor = new Nepada\BustCache\BustCachePathProcessor($fileSystem, $cache, $strategy);
$latte->addExtension(new Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension($pathProcessor, $autoRefresh));
```


Usage
-----

Example:

```latte
<link rel="stylesheet" href="{bustCache /css/style.css}">
```

The resulting path depends on the (auto-)chosen cache busting strategy:

```latte
<!-- modificationTime: timestamp of the last file modification -->
<link rel="stylesheet" href="/css/style.css?1449177985">

<!-- contentHash:  first 10 letters of md5 hash of the file content -->
<link rel="stylesheet" href="/css/style.css?a1d0c6e83a">
```


### Caching

The caching is implemented on two levels - runtime and compile-time.

#### Runtime caching

The cache stores the computed cache busted path for each input path.

DI extension automatically enables this cache, if you have `nette/caching` configured. In production mode with default settings, asset files are not checked for modification to avoid unnecessary I/O, i.e. the cache is not automatically refreshed.

#### Compile time caching

When the file path is specified as literal string, the cache busted path is computed in compile time of Latte template and the cache busted path is directly dumped into the compiled code of template.

With the default settings, this is enabled only in production mode.

#### Cache busting of files that are modified in app runtime

If you want to use cache busting on files that are expected to be modified in app runtime, you can use `dynamic` keyword to opt-out of compile time caching and force auto refresh of cache even in production mode:

```latte
<link rel="stylesheet" href="{bustCache dynamic /css/theme.css}">
```
