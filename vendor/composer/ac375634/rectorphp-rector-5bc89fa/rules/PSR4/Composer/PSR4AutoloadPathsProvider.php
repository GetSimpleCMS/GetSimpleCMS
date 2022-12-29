<?php

declare (strict_types=1);
namespace Rector\PSR4\Composer;

use RectorPrefix202212\Nette\Utils\FileSystem;
use RectorPrefix202212\Nette\Utils\Json;
final class PSR4AutoloadPathsProvider
{
    /**
     * @var array<string, array<string, string>>
     */
    private $cachedComposerJsonPSR4AutoloadPaths = [];
    /**
     * @return array<string, string|string[]>
     */
    public function provide() : array
    {
        if ($this->cachedComposerJsonPSR4AutoloadPaths !== []) {
            return $this->cachedComposerJsonPSR4AutoloadPaths;
        }
        $fileContents = FileSystem::read($this->getComposerJsonPath());
        $composerJson = Json::decode($fileContents, Json::FORCE_ARRAY);
        $psr4Autoloads = \array_merge($composerJson['autoload']['psr-4'] ?? [], $composerJson['autoload-dev']['psr-4'] ?? []);
        $this->cachedComposerJsonPSR4AutoloadPaths = $this->removeEmptyNamespaces($psr4Autoloads);
        return $this->cachedComposerJsonPSR4AutoloadPaths;
    }
    private function getComposerJsonPath() : string
    {
        // assume the project has "composer.json" in root directory
        return \getcwd() . '/composer.json';
    }
    /**
     * @param array<string, array<string, string>> $psr4Autoloads
     * @return array<string, array<string, string>>
     */
    private function removeEmptyNamespaces(array $psr4Autoloads) : array
    {
        return \array_filter($psr4Autoloads, static function (string $psr4Autoload) : bool {
            return $psr4Autoload !== '';
        }, \ARRAY_FILTER_USE_KEY);
    }
}
