<?php

declare (strict_types=1);
namespace PHPStan\PhpDocParser\Ast\Type;

use PHPStan\PhpDocParser\Ast\NodeAttributes;
class OffsetAccessTypeNode implements \PHPStan\PhpDocParser\Ast\Type\TypeNode
{
    use NodeAttributes;
    /** @var TypeNode */
    public $type;
    /** @var TypeNode */
    public $offset;
    public function __construct(\PHPStan\PhpDocParser\Ast\Type\TypeNode $type, \PHPStan\PhpDocParser\Ast\Type\TypeNode $offset)
    {
        $this->type = $type;
        $this->offset = $offset;
    }
    public function __toString() : string
    {
        return $this->type . '[' . $this->offset . ']';
    }
}
