<?php

declare (strict_types=1);
namespace Rector\DowngradePhp56\Rector\Pow;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Pow;
use PhpParser\Node\Expr\FuncCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @changelog https://wiki.php.net/rfc/pow-operator
 *
 * @see \Rector\Tests\DowngradePhp56\Rector\Pow\DowngradeExponentialOperatorRector\DowngradeExponentialOperatorRectorTest
 */
final class DowngradeExponentialOperatorRector extends AbstractRector
{
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Changes ** (exp) operator to pow(val, val2)', [new CodeSample('1**2;', 'pow(1, 2);')]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [Pow::class];
    }
    /**
     * @param Pow $node
     */
    public function refactor(Node $node) : FuncCall
    {
        return $this->nodeFactory->createFuncCall('pow', [$node->left, $node->right]);
    }
}
