<?php

declare (strict_types=1);
namespace Rector\Php81\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PHPStan\Reflection\ClassReflection;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\Reflection\ReflectionResolver;
use Rector\Core\ValueObject\MethodName;
use Rector\Core\ValueObject\PhpVersionFeature;
use Rector\FamilyTree\NodeAnalyzer\ClassChildAnalyzer;
use Rector\Php81\NodeAnalyzer\ComplexNewAnalyzer;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://wiki.php.net/rfc/new_in_initializers
 *
 * @see \Rector\Tests\Php81\Rector\ClassMethod\NewInInitializerRector\NewInInitializerRectorTest
 */
final class NewInInitializerRector extends AbstractRector implements MinPhpVersionInterface
{
    /**
     * @readonly
     * @var \Rector\Php81\NodeAnalyzer\ComplexNewAnalyzer
     */
    private $complexNewAnalyzer;
    /**
     * @readonly
     * @var \Rector\Core\Reflection\ReflectionResolver
     */
    private $reflectionResolver;
    /**
     * @readonly
     * @var \Rector\FamilyTree\NodeAnalyzer\ClassChildAnalyzer
     */
    private $classChildAnalyzer;
    public function __construct(ComplexNewAnalyzer $complexNewAnalyzer, ReflectionResolver $reflectionResolver, ClassChildAnalyzer $classChildAnalyzer)
    {
        $this->complexNewAnalyzer = $complexNewAnalyzer;
        $this->reflectionResolver = $reflectionResolver;
        $this->classChildAnalyzer = $classChildAnalyzer;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Replace property declaration of new state with direct new', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    private Logger $logger;

    public function __construct(
        ?Logger $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger;
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(
        private Logger $logger = new NullLogger,
    ) {
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
        return [ClassMethod::class];
    }
    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node) : ?Node
    {
        if (!$this->isLegalClass($node)) {
            return null;
        }
        $params = $this->matchConstructorParams($node);
        if ($params === []) {
            return null;
        }
        if ($this->isOverrideAbstractMethod($node)) {
            return null;
        }
        foreach ($params as $param) {
            /** @var string $paramName */
            $paramName = $this->getName($param->var);
            $toPropertyAssigns = $this->betterNodeFinder->findClassMethodAssignsToLocalProperty($node, $paramName);
            $toPropertyAssigns = \array_filter($toPropertyAssigns, static function (Assign $assign) : bool {
                return $assign->expr instanceof Coalesce;
            });
            foreach ($toPropertyAssigns as $toPropertyAssign) {
                /** @var Coalesce $coalesce */
                $coalesce = $toPropertyAssign->expr;
                if (!$coalesce->right instanceof New_) {
                    continue;
                }
                if ($this->complexNewAnalyzer->isDynamic($coalesce->right)) {
                    continue;
                }
                /** @var NullableType $currentParamType */
                $currentParamType = $param->type;
                $param->type = $currentParamType->type;
                $param->default = $coalesce->right;
                $this->removeNode($toPropertyAssign);
                $this->processPropertyPromotion($node, $param, $paramName);
            }
        }
        return $node;
    }
    public function provideMinPhpVersion() : int
    {
        return PhpVersionFeature::NEW_INITIALIZERS;
    }
    private function isOverrideAbstractMethod(ClassMethod $classMethod) : bool
    {
        $classReflection = $this->reflectionResolver->resolveClassReflection($classMethod);
        $methodName = $this->nodeNameResolver->getName($classMethod);
        return $classReflection instanceof ClassReflection && $this->classChildAnalyzer->hasAbstractParentClassMethod($classReflection, $methodName);
    }
    private function processPropertyPromotion(ClassMethod $classMethod, Param $param, string $paramName) : void
    {
        $classLike = $this->betterNodeFinder->findParentType($classMethod, ClassLike::class);
        if (!$classLike instanceof ClassLike) {
            return;
        }
        $property = $classLike->getProperty($paramName);
        if (!$property instanceof Property) {
            return;
        }
        $param->flags = $property->flags;
        $this->removeNode($property);
    }
    private function isLegalClass(ClassMethod $classMethod) : bool
    {
        $classLike = $this->betterNodeFinder->findParentType($classMethod, ClassLike::class);
        if ($classLike instanceof Interface_) {
            return \false;
        }
        if ($classLike instanceof Class_) {
            return !$classLike->isAbstract();
        }
        return \true;
    }
    /**
     * @return Param[]
     */
    private function matchConstructorParams(ClassMethod $classMethod) : array
    {
        if (!$this->isName($classMethod, MethodName::CONSTRUCT)) {
            return [];
        }
        if ($classMethod->params === []) {
            return [];
        }
        if ((array) $classMethod->stmts === []) {
            return [];
        }
        return \array_filter($classMethod->params, static function (Param $param) : bool {
            return $param->type instanceof NullableType;
        });
    }
}
