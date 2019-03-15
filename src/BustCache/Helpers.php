<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Latte;

class Helpers
{

    use Latte\Strict;

    final public function __construct()
    {
        throw new StaticClassException();
    }

    /**
     * @param string $file
     * @return string
     * @throws FileNotFoundException
     */
    public static function timestamp(string $file): string
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $timestamp = @filemtime($file) ?: time();

        return (string) $timestamp;
    }

    /**
     * @param string $file
     * @return string
     * @throws IOException
     */
    public static function hash(string $file): string
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            throw new IOException("Unable to read file '$file'.");
        }

        return substr(md5($content), 0, 10);
    }

}
