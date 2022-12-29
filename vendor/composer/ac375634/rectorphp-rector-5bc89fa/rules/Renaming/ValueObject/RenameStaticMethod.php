<?php

declare (strict_types=1);
namespace Rector\Renaming\ValueObject;

use PHPStan\Type\ObjectType;
use Rector\Core\Validation\RectorAssert;
final class RenameStaticMethod
{
    /**
     * @readonly
     * @var string
     */
    private $oldClass;
    /**
     * @readonly
     * @var string
     */
    private $oldMethod;
    /**
     * @readonly
     * @var string
     */
    private $newClass;
    /**
     * @readonly
     * @var string
     */
    private $newMethod;
    public function __construct(string $oldClass, string $oldMethod, string $newClass, string $newMethod)
    {
        $this->oldClass = $oldClass;
        $this->oldMethod = $oldMethod;
        $this->newClass = $newClass;
        $this->newMethod = $newMethod;
        RectorAssert::className($oldClass);
        RectorAssert::methodName($oldMethod);
        RectorAssert::className($newClass);
        RectorAssert::methodName($newMethod);
    }
    public function getOldObjectType() : ObjectType
    {
        return new ObjectType($this->oldClass);
    }
    public function getOldMethod() : string
    {
        return $this->oldMethod;
    }
    public function getNewClass() : string
    {
        return $this->newClass;
    }
    public function getNewMethod() : string
    {
        return $this->newMethod;
    }
    public function hasClassChanged() : bool
    {
        return $this->oldClass !== $this->newClass;
    }
}
