<?php
declare(strict_types = 1);

namespace Nepada\BustCache\Manifest;

use Nette\Utils\JsonException;

final class InvalidManifestException extends \RuntimeException
{

    public static function invalidJson(string $filePath, JsonException $exception): self
    {
        return new self("Manifest file '{$filePath}' does not contain a valid json", 0, $exception);
    }

    public static function unexpectedContent(string $filePath): self
    {
        return new self("Manifest file '{$filePath}' does not contain an expected shape for revision map");
    }

}
