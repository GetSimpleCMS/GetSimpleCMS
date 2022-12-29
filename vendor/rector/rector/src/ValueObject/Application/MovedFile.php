<?php

declare (strict_types=1);
namespace Rector\Core\ValueObject\Application;

use PhpParser\Node\Stmt;
use Rector\FileSystemRector\Contract\FileWithNodesInterface;
final class MovedFile implements FileWithNodesInterface
{
    /**
     * @readonly
     * @var \Rector\Core\ValueObject\Application\File
     */
    private $file;
    /**
     * @readonly
     * @var string
     */
    private $newFilePath;
    public function __construct(\Rector\Core\ValueObject\Application\File $file, string $newFilePath)
    {
        $this->file = $file;
        $this->newFilePath = $newFilePath;
    }
    public function getFile() : \Rector\Core\ValueObject\Application\File
    {
        return $this->file;
    }
    public function getNewFilePath() : string
    {
        return $this->newFilePath;
    }
    /**
     * @return Stmt[]
     */
    public function getNodes() : array
    {
        return $this->file->getNewStmts();
    }
    public function getFilePath() : string
    {
        return $this->file->getFilePath();
    }
}
