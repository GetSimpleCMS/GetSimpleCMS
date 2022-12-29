<?php

declare (strict_types=1);
namespace Rector\DeadCode\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\If_;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector\RemoveAlwaysTrueIfConditionRectorTest
 */
final class RemoveAlwaysTrueIfConditionRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Core\Reflection\ReflectionResolver
     */
    private $reflectionResolver;
    public function __construct(ReflectionResolver $reflectionResolver)
    {
        $this->reflectionResolver = $reflectionResolver;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Remove if condition that is always true', [new CodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    public function go()
    {
        if (1 === 1) {
            return 'yes';
        }

        return 'no';
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    public function go()
    {
        return 'yes';

        return 'no';
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
        return [If_::class];
    }
    /**
     * @param If_ $node
     * @return If_|null|Stmt[]
     */
    public function refactor(Node $node)
    {
        if ($node->else !== null) {
            return null;
        }
        // just one if
        if ($node->elseifs !== []) {
            return null;
        }
        $conditionStaticType = $this->getType($node->cond);
        if (!$conditionStaticType instanceof ConstantBooleanType) {
            return null;
        }
        if (!$conditionStaticType->getValue()) {
            return null;
        }
        if ($this->shouldSkipPropertyFetch($node->cond)) {
            return null;
        }
        if ($node->stmts === []) {
            $this->removeNode($node);
            return null;
        }
        return $node->stmts;
    }
    private function shouldSkipPropertyFetch(Expr $expr) : bool
    {
        /** @var PropertyFetch[]|StaticPropertyFetch[] $propertyFetches */
        $propertyFetches = $this->betterNodeFinder->findInstancesOf($expr, [PropertyFetch::class, StaticPropertyFetch::class]);
        foreach ($propertyFetches as $propertyFetch) {
            $classReflection = $this->reflectionResolver->resolveClassReflectionSourceObject($propertyFetch);
            if (!$classReflection instanceof ClassReflection) {
                // cannot get parent Trait_ from Property Fetch
                return \true;
            }
            $propertyName = (string) $this->nodeNameResolver->getName($propertyFetch);
            if (!$classReflection->hasNativeProperty($propertyName)) {
                continue;
            }
            $nativeProperty = $classReflection->getNativeProperty($propertyName);
            if (!$nativeProperty->hasNativeType()) {
                return \true;
            }
        }
        return \false;
    }
}
