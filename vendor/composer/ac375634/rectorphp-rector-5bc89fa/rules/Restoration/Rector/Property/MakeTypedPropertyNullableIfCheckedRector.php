<?php

declare (strict_types=1);
namespace Rector\Restoration\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use Rector\Core\Rector\AbstractRector;
use Rector\Privatization\NodeManipulator\VisibilityManipulator;
use Rector\TypeDeclaration\AlreadyAssignDetector\ConstructorAssignDetector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\Restoration\Rector\Property\MakeTypedPropertyNullableIfCheckedRector\MakeTypedPropertyNullableIfCheckedRectorTest
 */
final class MakeTypedPropertyNullableIfCheckedRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Privatization\NodeManipulator\VisibilityManipulator
     */
    private $visibilityManipulator;
    /**
     * @readonly
     * @var \Rector\TypeDeclaration\AlreadyAssignDetector\ConstructorAssignDetector
     */
    private $constructorAssignDetector;
    public function __construct(VisibilityManipulator $visibilityManipulator, ConstructorAssignDetector $constructorAssignDetector)
    {
        $this->visibilityManipulator = $visibilityManipulator;
        $this->constructorAssignDetector = $constructorAssignDetector;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Make typed property nullable if checked', [new CodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    private AnotherClass $anotherClass;

    public function run()
    {
        if ($this->anotherClass === null) {
            $this->anotherClass = new AnotherClass;
        }
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    private ?AnotherClass $anotherClass = null;

    public function run()
    {
        if ($this->anotherClass === null) {
            $this->anotherClass = new AnotherClass;
        }
    }
}
CODE_SAMPLE
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Property::class];
    }
    /**
     * @param Property $node
     */
    public function refactor(Node $node) : ?Node
    {
        if ($this->shouldSkipProperty($node)) {
            return null;
        }
        /** @var PropertyProperty $onlyProperty */
        $onlyProperty = $node->props[0];
        //Skip properties with default values
        if ($onlyProperty->default instanceof Expr) {
            return null;
        }
        $classLike = $this->betterNodeFinder->findParentType($onlyProperty, Class_::class);
        if (!$classLike instanceof Class_) {
            return null;
        }
        $isPropertyConstructorAssigned = $this->isPropertyConstructorAssigned($classLike, $onlyProperty);
        if ($isPropertyConstructorAssigned) {
            return null;
        }
        $isPropertyNullChecked = $this->isPropertyNullChecked($classLike, $onlyProperty);
        if (!$isPropertyNullChecked) {
            return null;
        }
        if ($node->type instanceof ComplexType) {
            return null;
        }
        $currentPropertyType = $node->type;
        if ($currentPropertyType === null) {
            return null;
        }
        $node->type = new NullableType($currentPropertyType);
        $onlyProperty->default = $this->nodeFactory->createNull();
        if ($node->isReadonly()) {
            $this->visibilityManipulator->removeReadonly($node);
        }
        return $node;
    }
    private function shouldSkipProperty(Property $property) : bool
    {
        if (\count($property->props) !== 1) {
            return \true;
        }
        if ($property->type === null) {
            return \true;
        }
        return $property->type instanceof NullableType;
    }
    private function isPropertyConstructorAssigned(Class_ $class, PropertyProperty $onlyPropertyProperty) : bool
    {
        $propertyName = $this->nodeNameResolver->getName($onlyPropertyProperty);
        return $this->constructorAssignDetector->isPropertyAssigned($class, $propertyName);
    }
    private function isPropertyNullChecked(Class_ $class, PropertyProperty $onlyPropertyProperty) : bool
    {
        if ($this->isIdenticalOrNotIdenticalToNull($class, $onlyPropertyProperty)) {
            return \true;
        }
        return $this->isBooleanNot($class, $onlyPropertyProperty);
    }
    private function isIdenticalOrNotIdenticalToNull(Class_ $class, PropertyProperty $onlyPropertyProperty) : bool
    {
        $isIdenticalOrNotIdenticalToNull = \false;
        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use($onlyPropertyProperty, &$isIdenticalOrNotIdenticalToNull) {
            $matchedPropertyFetchName = $this->matchPropertyFetchNameComparedToNull($node);
            if ($matchedPropertyFetchName === null) {
                return null;
            }
            if (!$this->isName($onlyPropertyProperty, $matchedPropertyFetchName)) {
                return null;
            }
            $isIdenticalOrNotIdenticalToNull = \true;
        });
        return $isIdenticalOrNotIdenticalToNull;
    }
    private function isBooleanNot(Class_ $class, PropertyProperty $onlyPropertyProperty) : bool
    {
        $isBooleanNot = \false;
        $this->traverseNodesWithCallable($class->stmts, function (Node $node) use($onlyPropertyProperty, &$isBooleanNot) {
            if (!$node instanceof BooleanNot) {
                return null;
            }
            if (!$node->expr instanceof PropertyFetch) {
                return null;
            }
            if (!$this->isName($node->expr->var, 'this')) {
                return null;
            }
            if (!$this->nodeNameResolver->areNamesEqual($onlyPropertyProperty, $node->expr->name)) {
                return null;
            }
            $isBooleanNot = \true;
        });
        return $isBooleanNot;
    }
    /**
     * Matches:
     * $this-><someProprety> === null
     * null === $this-><someProprety>
     */
    private function matchPropertyFetchNameComparedToNull(Node $node) : ?string
    {
        if (!$node instanceof Identical && !$node instanceof NotIdentical) {
            return null;
        }
        if ($node->left instanceof PropertyFetch && $this->valueResolver->isNull($node->right)) {
            $propertyFetch = $node->left;
        } elseif ($node->right instanceof PropertyFetch && $this->valueResolver->isNull($node->left)) {
            $propertyFetch = $node->right;
        } else {
            return null;
        }
        if (!$this->isName($propertyFetch->var, 'this')) {
            return null;
        }
        return $this->getName($propertyFetch->name);
    }
}
