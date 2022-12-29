<?php

declare (strict_types=1);
namespace Rector\DeadCode\Rector\Assign;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\Core\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Core\Rector\AbstractRector;
use Rector\DeadCode\SideEffect\SideEffectNodeDetector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\DeadCode\Rector\Assign\RemoveDoubleAssignRector\RemoveDoubleAssignRectorTest
 */
final class RemoveDoubleAssignRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\DeadCode\SideEffect\SideEffectNodeDetector
     */
    private $sideEffectNodeDetector;
    public function __construct(SideEffectNodeDetector $sideEffectNodeDetector)
    {
        $this->sideEffectNodeDetector = $sideEffectNodeDetector;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Simplify useless double assigns', [new CodeSample(<<<'CODE_SAMPLE'
$value = 1;
$value = 1;
CODE_SAMPLE
, '$value = 1;')]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Foreach_::class, FileWithoutNamespace::class, ClassMethod::class, Function_::class, Closure::class, If_::class, Namespace_::class];
    }
    /**
     * @param Foreach_|FileWithoutNamespace|If_|Namespace_|ClassMethod|Function_|Closure $node
     */
    public function refactor(Node $node) : ?Node
    {
        $stmts = $node->stmts;
        if ($stmts === null) {
            return null;
        }
        $hasRemovedStmt = \false;
        foreach ($stmts as $key => $stmt) {
            if (!isset($stmts[$key + 1])) {
                continue;
            }
            if (!$stmt instanceof Expression) {
                continue;
            }
            $nextStmt = $stmts[$key + 1];
            if (!$nextStmt instanceof Expression) {
                continue;
            }
            if (!$stmt->expr instanceof Assign) {
                continue;
            }
            if (!$nextStmt->expr instanceof Assign) {
                continue;
            }
            $nextAssign = $nextStmt->expr;
            if (!$this->nodeComparator->areNodesEqual($nextAssign->var, $stmt->expr->var)) {
                continue;
            }
            // early check self referencing, ensure that variable not re-used
            if ($this->isSelfReferencing($nextAssign)) {
                continue;
            }
            // detect call expression has side effect
            // no calls on right, could hide e.g. array_pop()|array_shift()
            if ($this->sideEffectNodeDetector->detectCallExpr($stmt->expr->expr)) {
                continue;
            }
            if (!$stmt->expr->var instanceof Variable && !$stmt->expr->var instanceof PropertyFetch && !$stmt->expr->var instanceof StaticPropertyFetch) {
                continue;
            }
            // remove current Stmt if will be overriden in next stmt
            $this->removeNode($stmt);
            $hasRemovedStmt = \true;
        }
        if (!$hasRemovedStmt) {
            return null;
        }
        return $node;
    }
    private function isSelfReferencing(Assign $assign) : bool
    {
        return (bool) $this->betterNodeFinder->findFirst($assign->expr, function (Node $subNode) use($assign) : bool {
            return $this->nodeComparator->areNodesEqual($assign->var, $subNode);
        });
    }
}
