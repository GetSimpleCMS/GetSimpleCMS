<?php

declare (strict_types=1);
namespace Rector\Php80\Guard;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use Rector\Php74\Guard\PropertyTypeChangeGuard;
final class MakePropertyPromotionGuard
{
    /**
     * @readonly
     * @var \Rector\Php74\Guard\PropertyTypeChangeGuard
     */
    private $propertyTypeChangeGuard;
    public function __construct(PropertyTypeChangeGuard $propertyTypeChangeGuard)
    {
        $this->propertyTypeChangeGuard = $propertyTypeChangeGuard;
    }
    public function isLegal(Class_ $class, Property $property, Param $param, bool $inlinePublic = \true) : bool
    {
        if (!$this->propertyTypeChangeGuard->isLegal($property, $inlinePublic, \true)) {
            return \false;
        }
        if ($class->isFinal()) {
            return \true;
        }
        if ($inlinePublic) {
            return \true;
        }
        if ($property->isPrivate()) {
            return \true;
        }
        if (!$param->type instanceof Node) {
            return \true;
        }
        return $property->type instanceof Node;
    }
}
