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
  manifest: true
  strictMode: false
```

If you're using stand-alone Latte, install the Latte extension manually:

```php
$fileSystem = Nepada\BustCache\LocalFileSystem::forDirectory($wwwDir);
$manifestFinder = new Nepada\BustCache\Manifest\AutodetectManifestFinder($fileSystem);
$revisionFinder = new Nepada\BustCache\Manifest\DefaultRevisionFinder($fileSystem, $manifestFinder);
$cache = new Nepada\BustCache\Caching\NullCache(); // or other implementation of Cache
$strategy = new Nepada\BustCache\CacheBustingStrategies\ContentHash(); // or other strategy
$pathProcessor = new Nepada\BustCache\BustCachePathProcessor($fileSystem, $cache, $revisionFinder, $strategy);
$latte->addExtension(new Nepada\Bridges\BustCacheLatte\BustCacheLatteExtension($pathProcessor, $strictMode, $autoRefresh));
```


Usage
-----

Basic example:

```latte
<link rel="stylesheet" href="{bustCache /css/style.css}">
```

The resulting path depends on the (auto-)chosen cache busting strategy:

```latte
<!-- using path from revision manifest -->
<link rel="stylesheet" href="/css/style-30cc681d44.css">

<!-- using query cache busting with modificationTime strategy: timestamp of the last file modification -->
<link rel="stylesheet" href="/css/style.css?1449177985">

<!-- using query cache busting with contentHash strategy: first 10 letters of md5 hash of the file content -->
<link rel="stylesheet" href="/css/style.css?a1d0c6e83a">
```

Usage in application with non-trivial base path:

```latte
<link rel="stylesheet" href="{$basePath}{bustCache /css/style.css}">
```

Generating full absolute URL:

```latte
<link rel="stylesheet" href="{$baseUrl}{bustCache /css/style.css}">
```


### Revision manifest support

Revision manifest is a JSON file that contains mapping between original asset path and its revision path.

Example:
```json
{
    "css/style.css": "css/style-30cc681d44.css",
    "js/app.js": "js/app-68130ccd44.js"
}
```


#### Configuration

With default configuration the path of manifest file is auto-detected by traversing up from asset directory and looking for `manifest.json` or `rev-manifest.json`. If a manifest file is found, the contained revision mapping is used instead of cache busting using query parameter.

You can completely disable the revision manifest support by setting `manifest: false` in your config.

You can also bypass the auto-detection and specify the manifest file path statically, e.g. `manifest: "assets/my-manifest.json"`


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


### Handling of missing manifest or asset files

With default configuration, when a missing file (manifest or asset) is encountered a warning triggered and asset dummy path is generated. You can switch to `strictMode: true` to fail hard by throwing exception instead.

"Missing file" is one of the following cases:
- the static manifest file specified in configuration does not exist
- a manifest file points to a revision path that does not exist
- using cache busting by query parameter with asset path that does not exist
