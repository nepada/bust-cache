<?php
/**
 * This file is part of the nepada/bust-cache.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\BustCache;


class Helpers
{

    /**
     * @throws StaticClassException
     */
    final public function __construct()
    {
        throw new StaticClassException;
    }

    /**
     * @param string $file
     * @return string
     * @throws FileNotFoundException
     */
    public static function timestamp($file)
    {
        if (!file_exists($file)) {
            throw FileNotFoundException::fromFile($file);
        }

        return @filemtime($file) ?: time();
    }

    /**
     * @param string $file
     * @return string
     * @throws FileNotFoundException
     */
    public static function hash($file)
    {
        if (!file_exists($file)) {
            throw FileNotFoundException::fromFile($file);
        }

        return substr(md5(file_get_contents($file)), 0, 10);
    }

}
