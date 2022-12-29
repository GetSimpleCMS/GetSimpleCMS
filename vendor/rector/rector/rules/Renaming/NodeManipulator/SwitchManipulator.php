<?php

declare (strict_types=1);
namespace Rector\Renaming\NodeManipulator;

use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Break_;
final class SwitchManipulator
{
    /**
     * @param Stmt[] $stmts
     * @return Stmt[]
     */
    public function removeBreakNodes(array $stmts) : array
    {
        foreach ($stmts as $key => $node) {
            if (!$node instanceof Break_) {
                continue;
            }
            if (!$node->num instanceof LNumber || $node->num->value === 1) {
                unset($stmts[$key]);
                continue;
            }
            $node->num = $node->num->value === 2 ? null : new LNumber($node->num->value - 1);
        }
        return $stmts;
    }
}
