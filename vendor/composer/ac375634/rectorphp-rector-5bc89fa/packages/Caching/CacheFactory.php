<?php

declare (strict_types=1);
namespace Rector\Caching;

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Caching\ValueObject\Storage\MemoryCacheStorage;
use Rector\Core\Configuration\Option;
use Rector\Core\Configuration\Parameter\ParameterProvider;
use RectorPrefix202212\Symfony\Component\Filesystem\Filesystem;
final class CacheFactory
{
    /**
     * @readonly
     * @var \Rector\Core\Configuration\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fileSystem;
    public function __construct(ParameterProvider $parameterProvider, Filesystem $fileSystem)
    {
        $this->parameterProvider = $parameterProvider;
        $this->fileSystem = $fileSystem;
    }
    /**
     * @api config factory
     */
    public function create() : \Rector\Caching\Cache
    {
        $cacheDirectory = $this->parameterProvider->provideStringParameter(Option::CACHE_DIR);
        $cacheClass = FileCacheStorage::class;
        if ($this->parameterProvider->hasParameter(Option::CACHE_CLASS)) {
            $cacheClass = $this->parameterProvider->provideStringParameter(Option::CACHE_CLASS);
        }
        if ($cacheClass === FileCacheStorage::class) {
            // ensure cache directory exists
            if (!$this->fileSystem->exists($cacheDirectory)) {
                $this->fileSystem->mkdir($cacheDirectory);
            }
            $fileCacheStorage = new FileCacheStorage($cacheDirectory, $this->fileSystem);
            return new \Rector\Caching\Cache($fileCacheStorage);
        }
        return new \Rector\Caching\Cache(new MemoryCacheStorage());
    }
}
