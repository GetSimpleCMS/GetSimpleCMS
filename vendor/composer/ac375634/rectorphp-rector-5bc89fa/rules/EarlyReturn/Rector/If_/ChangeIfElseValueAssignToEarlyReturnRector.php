<?php

declare (strict_types=1);
namespace Rector\EarlyReturn\Rector\If_;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\NodeManipulator\IfManipulator;
use Rector\Core\NodeManipulator\StmtsManipulator;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://engineering.helpscout.com/reducing-complexity-with-guard-clauses-in-php-and-javascript-74600fd865c7
 *
 * @see \Rector\Tests\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector\ChangeIfElseValueAssignToEarlyReturnRectorTest
 */
final class ChangeIfElseValueAssignToEarlyReturnRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Core\NodeManipulator\IfManipulator
     */
    private $ifManipulator;
    /**
     * @readonly
     * @var \Rector\Core\NodeManipulator\StmtsManipulator
     */
    private $stmtsManipulator;
    public function __construct(IfManipulator $ifManipulator, StmtsManipulator $stmtsManipulator)
    {
        $this->ifManipulator = $ifManipulator;
        $this->stmtsManipulator = $stmtsManipulator;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Change if/else value to early return', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        if ($this->hasDocBlock($tokens, $index)) {
            $docToken = $tokens[$this->getDocBlockIndex($tokens, $index)];
        } else {
            $docToken = null;
        }

        return $docToken;
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        if ($this->hasDocBlock($tokens, $index)) {
            return $tokens[$this->getDocBlockIndex($tokens, $index)];
        }
        return null;
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
     * @return Stmt[]|null
     */
    public function refactor(Node $node) : ?array
    {
        $nextNode = $node->getAttribute(AttributeKey::NEXT_NODE);
        if (!$nextNode instanceof Return_) {
            return null;
        }
        if ($nextNode->expr === null) {
            return null;
        }
        if (!$this->ifManipulator->isIfAndElseWithSameVariableAssignAsLastStmts($node, $nextNode->expr)) {
            return null;
        }
        \end($node->stmts);
        $lastIfStmtKey = \key($node->stmts);
        /** @var Assign $assign */
        $assign = $this->stmtsManipulator->getUnwrappedLastStmt($node->stmts);
        $returnLastIf = new Return_($assign->expr);
        $this->mirrorComments($returnLastIf, $assign);
        $node->stmts[$lastIfStmtKey] = $returnLastIf;
        $else = $node->else;
        if (!$else instanceof Else_) {
            throw new ShouldNotHappenException();
        }
        /** @var array<int, Stmt> $elseStmts */
        $elseStmts = $else->stmts;
        /** @var Assign $assign */
        $assign = $this->stmtsManipulator->getUnwrappedLastStmt($elseStmts);
        \end($elseStmts);
        $lastElseStmtKey = \key($elseStmts);
        $returnLastElse = new Return_($assign->expr);
        $this->mirrorComments($returnLastElse, $assign);
        $elseStmts[$lastElseStmtKey] = $returnLastElse;
        $node->else = null;
        $this->removeNode($nextNode);
        return \array_merge([$node], $elseStmts);
    }
}
