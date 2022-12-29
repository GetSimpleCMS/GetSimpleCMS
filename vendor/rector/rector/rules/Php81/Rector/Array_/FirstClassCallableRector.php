<?php

declare (strict_types=1);
namespace Rector\Php81\Rector\Array_;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Rector\Core\PhpParser\AstResolver;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Rector\Core\ValueObject\PhpVersion;
use Rector\NodeCollector\NodeAnalyzer\ArrayCallableMethodMatcher;
use Rector\NodeCollector\ValueObject\ArrayCallable;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://php.watch/versions/8.1/first-class-callable-syntax
 *
 * @see \Rector\Tests\Php81\Rector\Array_\FirstClassCallableRector\FirstClassCallableRectorTest
 */
final class FirstClassCallableRector extends AbstractScopeAwareRector implements MinPhpVersionInterface
{
    /**
     * @readonly
     * @var \Rector\NodeCollector\NodeAnalyzer\ArrayCallableMethodMatcher
     */
    private $arrayCallableMethodMatcher;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\AstResolver
     */
    private $astResolver;
    public function __construct(ArrayCallableMethodMatcher $arrayCallableMethodMatcher, AstResolver $astResolver)
    {
        $this->arrayCallableMethodMatcher = $arrayCallableMethodMatcher;
        $this->astResolver = $astResolver;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Upgrade array callable to first class callable', [new CodeSample(<<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $name = [$this, 'name'];
    }

    public function name()
    {
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $name = $this->name(...);
    }

    public function name()
    {
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
        return [Array_::class];
    }
    /**
     * @param Array_ $node
     */
    public function refactorWithScope(Node $node, Scope $scope)
    {
        $arrayCallable = $this->arrayCallableMethodMatcher->match($node);
        if (!$arrayCallable instanceof ArrayCallable) {
            return null;
        }
        $callerExpr = $arrayCallable->getCallerExpr();
        if (!$callerExpr instanceof Variable && !$callerExpr instanceof PropertyFetch && !$callerExpr instanceof ClassConstFetch) {
            return null;
        }
        $args = [new VariadicPlaceholder()];
        if ($callerExpr instanceof ClassConstFetch) {
            $type = $this->getType($callerExpr->class);
            if ($type instanceof FullyQualifiedObjectType && $this->isNonStaticOtherObject($type, $arrayCallable, $scope)) {
                return null;
            }
            return new StaticCall($callerExpr->class, $arrayCallable->getMethod(), $args);
        }
        return new MethodCall($callerExpr, $arrayCallable->getMethod(), $args);
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersion::PHP_81;
    }
    private function isNonStaticOtherObject(FullyQualifiedObjectType $fullyQualifiedObjectType, ArrayCallable $arrayCallable, Scope $scope) : bool
    {
        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection) {
            return \false;
        }
        if ($classReflection->getName() === $fullyQualifiedObjectType->getClassName()) {
            return \false;
        }
        $class = $this->astResolver->resolveClassFromName($arrayCallable->getClass());
        if (!$class instanceof ClassLike) {
            return \false;
        }
        $classMethod = $class->getMethod($arrayCallable->getMethod());
        if (!$classMethod instanceof ClassMethod) {
            return \false;
        }
        if (!$classMethod->isStatic()) {
            return \true;
        }
        return !$classMethod->isPublic();
    }
}
