<?php
declare(strict_types = 1);

namespace Nepada\BustCache\FileSystem;

final class LocalFileSystem implements FileSystem
{

    private Path $baseDirectoryPath;

    /**
     * @param Path $baseDirectoryPath
     * @throws DirectoryNotFoundException
     */
    public function __construct(Path $baseDirectoryPath)
    {
        $baseDirectoryPath = $baseDirectoryPath->normalize();
        if (! is_dir($baseDirectoryPath->toString())) {
            throw DirectoryNotFoundException::at($baseDirectoryPath->toString());
        }
        $this->baseDirectoryPath = $baseDirectoryPath;
    }

    /**
     * @param string $baseDir
     * @return LocalFileSystem
     * @throws DirectoryNotFoundException
     */
    public static function forDirectory(string $baseDir): self
    {
        return new self(Path::of($baseDir));
    }

    public function fileExists(Path $path): bool
    {
        try {
            $this->getFile($path);
            return true;
        } catch (FileNotFoundException $exception) {
            return false;
        }
    }

    /**
     * @param Path $path
     * @return File
     * @throws FileNotFoundException
     */
    public function getFile(Path $path): File
    {
        $fullLocalPath = Path::join($this->baseDirectoryPath, $path);
        if (! is_file($fullLocalPath->toString())) {
            throw FileNotFoundException::at($fullLocalPath->toString());
        }
        if (! str_starts_with((string) realpath($fullLocalPath->toString()), realpath($this->baseDirectoryPath->toString()) . DIRECTORY_SEPARATOR)) {
            throw FileNotFoundException::inBaseDirectory($fullLocalPath->toString(), $this->baseDirectoryPath->toString());
        }
        return File::fromLocalPath($fullLocalPath);
    }

}
