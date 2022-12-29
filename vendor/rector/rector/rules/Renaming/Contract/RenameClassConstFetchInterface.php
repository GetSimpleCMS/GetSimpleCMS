<?php

declare (strict_types=1);
namespace Rector\Renaming\Contract;

use PHPStan\Type\ObjectType;
interface RenameClassConstFetchInterface
{
    public function getOldObjectType() : ObjectType;
    public function getOldConstant() : string;
    public function getNewConstant() : string;
}
