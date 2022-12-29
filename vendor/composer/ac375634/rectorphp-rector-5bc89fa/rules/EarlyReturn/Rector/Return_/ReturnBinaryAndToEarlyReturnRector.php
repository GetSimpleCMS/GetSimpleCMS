<?php

declare (strict_types=1);
namespace Rector\EarlyReturn\Rector\Return_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Rector\Core\NodeAnalyzer\CallAnalyzer;
use Rector\Core\NodeManipulator\IfManipulator;
use Rector\Core\PhpParser\Node\AssignAndBinaryMap;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\EarlyReturn\Rector\Return_\ReturnBinaryAndToEarlyReturnRector\ReturnBinaryAndToEarlyReturnRectorTest
 */
final class ReturnBinaryAndToEarlyReturnRector extends AbstractRector
{
    /**
     * @readonly
     * @var \Rector\Core\NodeManipulator\IfManipulator
     */
    private $ifManipulator;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\AssignAndBinaryMap
     */
    private $assignAndBinaryMap;
    /**
     * @readonly
     * @var \Rector\Core\NodeAnalyzer\CallAnalyzer
     */
    private $callAnalyzer;
    public function __construct(IfManipulator $ifManipulator, AssignAndBinaryMap $assignAndBinaryMap, CallAnalyzer $callAnalyzer)
    {
        $this->ifManipulator = $ifManipulator;
        $this->assignAndBinaryMap = $assignAndBinaryMap;
        $this->callAnalyzer = $callAnalyzer;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Changes Single return of && to early returns', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function accept()
    {
        return $this->something() && $this->somethingelse();
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function accept()
    {
        if (! $this->something()) {
            return false;
        }

        return (bool) $this->somethingelse();
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
        return [Return_::class];
    }
    /**
     * @param Return_ $node
     * @return null|Node[]
     */
    public function refactor(Node $node) : ?array
    {
        if (!$node->expr instanceof BooleanAnd) {
            return null;
        }
        $left = $node->expr->left;
        $ifNegations = $this->createMultipleIfsNegation($left, $node, []);
        // ensure ifs not removed by other rules
        if ($ifNegations === []) {
            return null;
        }
        if (!$this->callAnalyzer->doesIfHasObjectCall($ifNegations)) {
            return null;
        }
        $this->mirrorComments($ifNegations[0], $node);
        /** @var BooleanAnd $booleanAnd */
        $booleanAnd = $node->expr;
        $lastReturnExpr = $this->assignAndBinaryMap->getTruthyExpr($booleanAnd->right);
        return \array_merge($ifNegations, [new Return_($lastReturnExpr)]);
    }
    /**
     * @param If_[] $ifNegations
     * @return If_[]
     */
    private function createMultipleIfsNegation(Expr $expr, Return_ $return, array $ifNegations) : array
    {
        while ($expr instanceof BooleanAnd) {
            $ifNegations = \array_merge($ifNegations, $this->collectLeftBooleanAndToIfs($expr, $return, $ifNegations));
            $ifNegations[] = $this->ifManipulator->createIfNegation($expr->right, new Return_($this->nodeFactory->createFalse()));
            $expr = $expr->right;
        }
        return $ifNegations + [$this->ifManipulator->createIfNegation($expr, new Return_($this->nodeFactory->createFalse()))];
    }
    /**
     * @param If_[] $ifNegations
     * @return If_[]
     */
    private function collectLeftBooleanAndToIfs(BooleanAnd $booleanAnd, Return_ $return, array $ifNegations) : array
    {
        $left = $booleanAnd->left;
        if (!$left instanceof BooleanAnd) {
            return [$this->ifManipulator->createIfNegation($left, new Return_($this->nodeFactory->createFalse()))];
        }
        return $this->createMultipleIfsNegation($left, $return, $ifNegations);
    }
}
