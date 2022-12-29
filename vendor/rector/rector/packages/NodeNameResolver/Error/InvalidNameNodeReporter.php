<?php

declare (strict_types=1);
namespace Rector\NodeNameResolver\Error;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Rector\Core\Contract\PhpParser\NodePrinterInterface;
use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\FileSystem\FilePathHelper;
use Rector\Core\Provider\CurrentFileProvider;
use Rector\Core\ValueObject\Application\File;
final class InvalidNameNodeReporter
{
    /**
     * @var string
     */
    private const FILE = 'file';
    /**
     * @readonly
     * @var \Rector\Core\Provider\CurrentFileProvider
     */
    private $currentFileProvider;
    /**
     * @readonly
     * @var \Rector\Core\Contract\PhpParser\NodePrinterInterface
     */
    private $nodePrinter;
    /**
     * @readonly
     * @var \Rector\Core\FileSystem\FilePathHelper
     */
    private $filePathHelper;
    public function __construct(CurrentFileProvider $currentFileProvider, NodePrinterInterface $nodePrinter, FilePathHelper $filePathHelper)
    {
        $this->currentFileProvider = $currentFileProvider;
        $this->nodePrinter = $nodePrinter;
        $this->filePathHelper = $filePathHelper;
    }
    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $node
     */
    public function reportInvalidNodeForName($node) : void
    {
        $message = \sprintf('Pick more specific node than "%s", e.g. "$node->name"', \get_class($node));
        $file = $this->currentFileProvider->getFile();
        if ($file instanceof File) {
            $message .= \PHP_EOL . \PHP_EOL;
            $relativeFilePath = $this->filePathHelper->relativePath($file->getFilePath());
            $message .= \sprintf('Caused in "%s" file on line %d on code "%s"', $relativeFilePath, $node->getStartLine(), $this->nodePrinter->print($node));
        }
        $backtrace = \debug_backtrace();
        $rectorBacktrace = $this->matchRectorBacktraceCall($backtrace);
        if ($rectorBacktrace !== null) {
            // issues to find the file in prefixed
            if (\file_exists($rectorBacktrace[self::FILE])) {
                $filePath = $rectorBacktrace[self::FILE];
                $relativeFilePath = $this->filePathHelper->relativePath($filePath);
                $fileAndLine = $relativeFilePath . ':' . $rectorBacktrace['line'];
            } else {
                $fileAndLine = $rectorBacktrace[self::FILE] . ':' . $rectorBacktrace['line'];
            }
            $message .= \PHP_EOL . \PHP_EOL;
            $message .= \sprintf('Look at "%s"', $fileAndLine);
        }
        throw new ShouldNotHappenException($message);
    }
    /**
     * @param mixed[] $backtrace
     * @return string[]|null
     */
    private function matchRectorBacktraceCall(array $backtrace) : ?array
    {
        foreach ($backtrace as $singleBacktrace) {
            if (!isset($singleBacktrace['object'])) {
                continue;
            }
            // match a Rector class
            if (!\is_a($singleBacktrace['object'], RectorInterface::class)) {
                continue;
            }
            return $singleBacktrace;
        }
        return $backtrace[1] ?? null;
    }
}
