<?php

declare (strict_types=1);
namespace Rector\PSR4\NodeManipulator;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PHPStan\Reflection\Constant\RuntimeConstantReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Core\Configuration\Option;
use Rector\Core\Configuration\Parameter\ParameterProvider;
use Rector\Core\Enum\ObjectReference;
use Rector\NodeNameResolver\NodeNameResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser;
final class FullyQualifyStmtsAnalyzer
{
    /**
     * @readonly
     * @var \Rector\Core\Configuration\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @readonly
     * @var \Rector\PhpDocParser\NodeTraverser\SimpleCallableNodeTraverser
     */
    private $simpleCallableNodeTraverser;
    /**
     * @readonly
     * @var \Rector\NodeNameResolver\NodeNameResolver
     */
    private $nodeNameResolver;
    /**
     * @readonly
     * @var \PHPStan\Reflection\ReflectionProvider
     */
    private $reflectionProvider;
    public function __construct(ParameterProvider $parameterProvider, SimpleCallableNodeTraverser $simpleCallableNodeTraverser, NodeNameResolver $nodeNameResolver, ReflectionProvider $reflectionProvider)
    {
        $this->parameterProvider = $parameterProvider;
        $this->simpleCallableNodeTraverser = $simpleCallableNodeTraverser;
        $this->nodeNameResolver = $nodeNameResolver;
        $this->reflectionProvider = $reflectionProvider;
    }
    /**
     * @param Stmt[] $stmts
     */
    public function process(array $stmts) : void
    {
        // no need to
        if ($this->parameterProvider->provideBoolParameter(Option::AUTO_IMPORT_NAMES)) {
            return;
        }
        // FQNize all class names
        $this->simpleCallableNodeTraverser->traverseNodesWithCallable($stmts, function (Node $node) : ?FullyQualified {
            if (!$node instanceof Name) {
                return null;
            }
            $name = $this->nodeNameResolver->getName($node);
            if (\in_array($name, [ObjectReference::STATIC, ObjectReference::PARENT, ObjectReference::SELF], \true)) {
                return null;
            }
            if ($this->isNativeConstant($node)) {
                return null;
            }
            $parentNode = $node->getAttribute(AttributeKey::PARENT_NODE);
            if ($parentNode instanceof GroupUse) {
                $parentNode->setAttribute(AttributeKey::ORIGINAL_NODE, null);
                return null;
            }
            if ($parentNode instanceof UseUse) {
                return null;
            }
            return new FullyQualified($name);
        });
    }
    private function isNativeConstant(Name $name) : bool
    {
        $parentNode = $name->getAttribute(AttributeKey::PARENT_NODE);
        if (!$parentNode instanceof ConstFetch) {
            return \false;
        }
        $scope = $name->getAttribute(AttributeKey::SCOPE);
        if (!$this->reflectionProvider->hasConstant($name, $scope)) {
            return \false;
        }
        $globalConstantReflection = $this->reflectionProvider->getConstant($name, $scope);
        return $globalConstantReflection instanceof RuntimeConstantReflection;
    }
}
