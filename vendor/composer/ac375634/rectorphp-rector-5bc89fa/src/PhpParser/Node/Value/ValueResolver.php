<?php

declare (strict_types=1);
namespace Rector\Core\PhpParser\Node\Value;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\MagicConst\File;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\TypeWithClassName;
use Rector\Core\Enum\ObjectReference;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\NodeAnalyzer\ConstFetchAnalyzer;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Core\Provider\CurrentFileProvider;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\NodeTypeResolver;
/**
 * @see \Rector\Core\Tests\PhpParser\Node\Value\ValueResolverTest
 * @todo make use of constant type of $scope->getType()
 */
final class ValueResolver
{
    /**
     * @var \PhpParser\ConstExprEvaluator|null
     */
    private $constExprEvaluator;
    /**
     * @readonly
     * @var \Rector\NodeNameResolver\NodeNameResolver
     */
    private $nodeNameResolver;
    /**
     * @readonly
     * @var \Rector\NodeTypeResolver\NodeTypeResolver
     */
    private $nodeTypeResolver;
    /**
     * @readonly
     * @var \Rector\Core\NodeAnalyzer\ConstFetchAnalyzer
     */
    private $constFetchAnalyzer;
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    /**
     * @readonly
     * @var \Rector\Core\Provider\CurrentFileProvider
     */
    private $currentFileProvider;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\BetterNodeFinder
     */
    private $betterNodeFinder;
    public function __construct(NodeNameResolver $nodeNameResolver, NodeTypeResolver $nodeTypeResolver, ConstFetchAnalyzer $constFetchAnalyzer, ReflectionProvider $reflectionProvider, CurrentFileProvider $currentFileProvider, BetterNodeFinder $betterNodeFinder)
    {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->nodeTypeResolver = $nodeTypeResolver;
        $this->constFetchAnalyzer = $constFetchAnalyzer;
        $this->reflectionProvider = $reflectionProvider;
        $this->currentFileProvider = $currentFileProvider;
        $this->betterNodeFinder = $betterNodeFinder;
    }
    /**
     * @param mixed $value
     */
    public function isValue(Expr $expr, $value) : bool
    {
        return $this->getValue($expr) === $value;
    }
    /**
     * @return mixed
     */
    public function getValue(Expr $expr, bool $resolvedClassReference = \false)
    {
        if ($expr instanceof Concat) {
            return $this->processConcat($expr, $resolvedClassReference);
        }
        if ($expr instanceof ClassConstFetch && $resolvedClassReference) {
            $class = $this->nodeNameResolver->getName($expr->class);
            if (\in_array($class, [ObjectReference::SELF, ObjectReference::STATIC], \true)) {
                // @todo scope is needed
                $classLike = $this->betterNodeFinder->findParentType($expr, ClassLike::class);
                if ($classLike instanceof ClassLike) {
                    return (string) $this->nodeNameResolver->getName($classLike);
                }
            }
            if ($this->nodeNameResolver->isName($expr->name, 'class')) {
                return $class;
            }
        }
        $value = $this->resolveExprValueForConst($expr);
        if ($value !== null) {
            return $value;
        }
        if ($expr instanceof ConstFetch) {
            return $this->nodeNameResolver->getName($expr);
        }
        $nodeStaticType = $this->nodeTypeResolver->getType($expr);
        if ($nodeStaticType instanceof ConstantArrayType) {
            return $this->extractConstantArrayTypeValue($nodeStaticType);
        }
        if ($nodeStaticType instanceof ConstantScalarType) {
            return $nodeStaticType->getValue();
        }
        return null;
    }
    /**
     * @api downgrade symfony
     * @param mixed[] $expectedValues
     */
    public function isValues(Expr $expr, array $expectedValues) : bool
    {
        foreach ($expectedValues as $expectedValue) {
            if ($this->isValue($expr, $expectedValue)) {
                return \true;
            }
        }
        return \false;
    }
    public function isFalse(Expr $expr) : bool
    {
        return $this->constFetchAnalyzer->isFalse($expr);
    }
    public function isTrueOrFalse(Expr $expr) : bool
    {
        return $this->constFetchAnalyzer->isTrueOrFalse($expr);
    }
    public function isTrue(Expr $expr) : bool
    {
        return $this->constFetchAnalyzer->isTrue($expr);
    }
    public function isNull(Expr $expr) : bool
    {
        return $this->constFetchAnalyzer->isNull($expr);
    }
    /**
     * @param Expr[]|null[] $nodes
     * @param mixed[] $expectedValues
     */
    public function areValuesEqual(array $nodes, array $expectedValues) : bool
    {
        foreach ($nodes as $i => $node) {
            if ($node === null) {
                return \false;
            }
            if (!$this->isValue($node, $expectedValues[$i])) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @return mixed
     */
    private function resolveExprValueForConst(Expr $expr)
    {
        try {
            $constExprEvaluator = $this->getConstExprEvaluator();
            return $constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException $exception) {
        }
        return null;
    }
    private function processConcat(Concat $concat, bool $resolvedClassReference) : string
    {
        return $this->getValue($concat->left, $resolvedClassReference) . $this->getValue($concat->right, $resolvedClassReference);
    }
    private function getConstExprEvaluator() : ConstExprEvaluator
    {
        if ($this->constExprEvaluator !== null) {
            return $this->constExprEvaluator;
        }
        $this->constExprEvaluator = new ConstExprEvaluator(function (Expr $expr) {
            if ($expr instanceof Dir) {
                // __DIR__
                return $this->resolveDirConstant();
            }
            if ($expr instanceof File) {
                // __FILE__
                return $this->resolveFileConstant($expr);
            }
            // resolve "SomeClass::SOME_CONST"
            if ($expr instanceof ClassConstFetch && $expr->class instanceof Name) {
                return $this->resolveClassConstFetch($expr);
            }
            throw new ConstExprEvaluationException(\sprintf('Expression of type "%s" cannot be evaluated', $expr->getType()));
        });
        return $this->constExprEvaluator;
    }
    /**
     * @return mixed[]|null
     */
    private function extractConstantArrayTypeValue(ConstantArrayType $constantArrayType) : ?array
    {
        $keys = [];
        foreach ($constantArrayType->getKeyTypes() as $i => $keyType) {
            /** @var ConstantScalarType $keyType */
            $keys[$i] = $keyType->getValue();
        }
        $values = [];
        foreach ($constantArrayType->getValueTypes() as $i => $valueType) {
            if ($valueType instanceof ConstantArrayType) {
                $value = $this->extractConstantArrayTypeValue($valueType);
            } elseif ($valueType instanceof ConstantScalarType) {
                $value = $valueType->getValue();
            } elseif ($valueType instanceof TypeWithClassName) {
                continue;
            } else {
                return null;
            }
            $values[$keys[$i]] = $value;
        }
        return $values;
    }
    private function resolveDirConstant() : string
    {
        $file = $this->currentFileProvider->getFile();
        if (!$file instanceof \Rector\Core\ValueObject\Application\File) {
            throw new ShouldNotHappenException();
        }
        return \dirname($file->getFilePath());
    }
    private function resolveFileConstant(File $file) : string
    {
        $file = $this->currentFileProvider->getFile();
        if (!$file instanceof \Rector\Core\ValueObject\Application\File) {
            throw new ShouldNotHappenException();
        }
        return $file->getFilePath();
    }
    /**
     * @return string|mixed
     */
    private function resolveClassConstFetch(ClassConstFetch $classConstFetch)
    {
        $class = $this->nodeNameResolver->getName($classConstFetch->class);
        $constant = $this->nodeNameResolver->getName($classConstFetch->name);
        if ($class === null) {
            throw new ShouldNotHappenException();
        }
        if ($constant === null) {
            throw new ShouldNotHappenException();
        }
        if (\in_array($class, [ObjectReference::SELF, ObjectReference::STATIC, ObjectReference::PARENT], \true)) {
            $class = $this->resolveClassFromSelfStaticParent($classConstFetch, $class);
        }
        if ($constant === 'class') {
            return $class;
        }
        $classConstantReference = $class . '::' . $constant;
        if (\defined($classConstantReference)) {
            return \constant($classConstantReference);
        }
        if (!$this->reflectionProvider->hasClass($class)) {
            // fallback to constant reference itself, to avoid fatal error
            return $classConstantReference;
        }
        $classReflection = $this->reflectionProvider->getClass($class);
        if (!$classReflection->hasConstant($constant)) {
            // fallback to constant reference itself, to avoid fatal error
            return $classConstantReference;
        }
        $constantReflection = $classReflection->getConstant($constant);
        $valueExpr = $constantReflection->getValueExpr();
        if ($valueExpr instanceof ConstFetch) {
            return $this->resolveExprValueForConst($valueExpr);
        }
        return $this->getValue($valueExpr);
    }
    private function resolveClassFromSelfStaticParent(ClassConstFetch $classConstFetch, string $class) : string
    {
        $classLike = $this->betterNodeFinder->findParentType($classConstFetch, ClassLike::class);
        if (!$classLike instanceof ClassLike) {
            throw new ShouldNotHappenException('Complete class parent node for to class const fetch, so "self" or "static" references is resolvable to a class name');
        }
        if ($class === ObjectReference::PARENT) {
            if (!$classLike instanceof Class_) {
                throw new ShouldNotHappenException('Complete class parent node for to class const fetch, so "parent" references is resolvable to lookup parent class');
            }
            if (!$classLike->extends instanceof FullyQualified) {
                throw new ShouldNotHappenException();
            }
            return $classLike->extends->toString();
        }
        return (string) $this->nodeNameResolver->getName($classLike);
    }
}
