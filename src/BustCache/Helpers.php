<?php
declare(strict_types = 1);

namespace Nepada\BustCache;

use Latte;


class Helpers
{

    use Latte\Strict;


    /**
     * @throws StaticClassException
     */
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
            throw FileNotFoundException::fromFile($file);
        }

        $timestamp = @filemtime($file) ?: time();

        return (string) $timestamp;
    }

    /**
     * @param string $file
     * @return string
     * @throws FileNotFoundException
     */
    public static function hash(string $file): string
    {
        if (!file_exists($file)) {
            throw FileNotFoundException::fromFile($file);
        }

        return substr(md5(file_get_contents($file)), 0, 10);
    }

}
