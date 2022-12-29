<?php

declare (strict_types=1);
namespace Rector\Arguments\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Core\Enum\ObjectReference;
use Rector\NodeNameResolver\NodeNameResolver;
final class ArgumentAddingScope
{
    /**
     * @api
     * @var string
     */
    public const SCOPE_PARENT_CALL = 'parent_call';
    /**
     * @api
     * @var string
     */
    public const SCOPE_METHOD_CALL = 'method_call';
    /**
     * @api
     * @var string
     */
    public const SCOPE_CLASS_METHOD = 'class_method';
    /**
     * @readonly
     * @var \Rector\NodeNameResolver\NodeNameResolver
     */
    private $nodeNameResolver;
    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }
    /**
     * @param \PhpParser\Node\Expr\MethodCall|\PhpParser\Node\Expr\StaticCall $expr
     */
    public function isInCorrectScope($expr, ArgumentAdder $argumentAdder) : bool
    {
        if ($argumentAdder->getScope() === null) {
            return \true;
        }
        $scope = $argumentAdder->getScope();
        if ($expr instanceof StaticCall) {
            if (!$expr->class instanceof Name) {
                return \false;
            }
            if ($this->nodeNameResolver->isName($expr->class, ObjectReference::PARENT)) {
                return $scope === self::SCOPE_PARENT_CALL;
            }
            return $scope === self::SCOPE_METHOD_CALL;
        }
        // MethodCall
        return $scope === self::SCOPE_METHOD_CALL;
    }
}
