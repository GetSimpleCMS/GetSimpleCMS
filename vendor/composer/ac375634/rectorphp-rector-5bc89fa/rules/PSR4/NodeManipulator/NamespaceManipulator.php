<?php

declare (strict_types=1);
namespace Rector\PSR4\NodeManipulator;

use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Namespace_;
final class NamespaceManipulator
{
    public function removeClassLikes(Namespace_ $namespace) : void
    {
        foreach ($namespace->stmts as $key => $namespaceStatement) {
            if (!$namespaceStatement instanceof ClassLike) {
                continue;
            }
            unset($namespace->stmts[$key]);
        }
    }
}
