<?php

declare (strict_types=1);
namespace Rector\Core\FileSystem;

/**
 * @see \Rector\Core\Tests\FileSystem\FileAndDirectoryFilter\FileAndDirectoryFilterTest
 */
final class FileAndDirectoryFilter
{
    /**
     * @param string[] $filesAndDirectories
     * @return string[]
     */
    public function filterDirectories(array $filesAndDirectories) : array
    {
        $directories = \array_filter($filesAndDirectories, static function (string $path) : bool {
            return !\is_file($path);
        });
        return \array_values($directories);
    }
    /**
     * @param string[] $filesAndDirectories
     * @return string[]
     */
    public function filterFiles(array $filesAndDirectories) : array
    {
        $files = \array_filter($filesAndDirectories, static function (string $path) : bool {
            return \is_file($path);
        });
        return \array_values($files);
    }
}
