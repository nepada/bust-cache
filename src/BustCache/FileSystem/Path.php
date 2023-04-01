<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

use Nette\Utils\FileSystem as NetteFileSystem;

final class Path
{

    private string $path;

    private function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function of(string $path): static
    {
        return new static($path);
    }

    public static function join(self|string ...$parts): static
    {
        $stringParts = array_map(
            fn (self|string $part): string => $part instanceof self ? $part->toString() : $part,
            $parts,
        );
        $result = array_shift($stringParts) ?? '';
        foreach ($stringParts as $part) {
            $result = rtrim($result, '/') . '/' . ltrim($part, '/');
        }
        return new static($result);
    }

    public function getDirectoryPath(): static
    {
        return new static(dirname($this->path));
    }

    public function normalize(): static
    {
        $normalized = NetteFileSystem::normalizePath($this->path);
        return new static(rtrim($normalized, '/'));
    }

    public function toString(): string
    {
        return $this->path;
    }

}
