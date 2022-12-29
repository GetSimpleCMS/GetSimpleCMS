<?php

declare (strict_types=1);
namespace Rector\TypeDeclaration\TypeInferer;

use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\TryCatch;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
final class SilentVoidResolver
{
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\BetterNodeFinder
     */
    private $betterNodeFinder;
    public function __construct(BetterNodeFinder $betterNodeFinder)
    {
        $this->betterNodeFinder = $betterNodeFinder;
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Expr\Closure|\PhpParser\Node\Stmt\Function_ $functionLike
     */
    public function hasExclusiveVoid($functionLike) : bool
    {
        $classLike = $this->betterNodeFinder->findParentType($functionLike, ClassLike::class);
        if ($classLike instanceof Interface_) {
            return \false;
        }
        if ($this->hasNeverType($functionLike)) {
            return \false;
        }
        if ($this->betterNodeFinder->hasInstancesOfInFunctionLikeScoped($functionLike, Yield_::class)) {
            return \false;
        }
        /** @var Return_[] $returns */
        $returns = $this->betterNodeFinder->findInstancesOfInFunctionLikeScoped($functionLike, Return_::class);
        foreach ($returns as $return) {
            if ($return->expr !== null) {
                return \false;
            }
        }
        return \true;
    }
    public function hasSilentVoid(FunctionLike $functionLike) : bool
    {
        if ($functionLike instanceof ArrowFunction) {
            return \false;
        }
        if ($this->hasStmtsAlwaysReturn((array) $functionLike->getStmts())) {
            return \false;
        }
        foreach ((array) $functionLike->getStmts() as $stmt) {
            // has switch with always return
            if ($stmt instanceof Switch_ && $this->isSwitchWithAlwaysReturn($stmt)) {
                return \false;
            }
            // is part of try/catch
            if ($stmt instanceof TryCatch && $this->isTryCatchAlwaysReturn($stmt)) {
                return \false;
            }
            if ($stmt instanceof Throw_) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @param Stmt[]|Expression[] $stmts
     */
    private function hasStmtsAlwaysReturn(array $stmts) : bool
    {
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Expression) {
                $stmt = $stmt->expr;
            }
            // is 1st level return
            if ($stmt instanceof Return_) {
                return \true;
            }
        }
        return \false;
    }
    private function isSwitchWithAlwaysReturn(Switch_ $switch) : bool
    {
        $hasDefault = \false;
        foreach ($switch->cases as $case) {
            if ($case->cond === null) {
                $hasDefault = \true;
                break;
            }
        }
        if (!$hasDefault) {
            return \false;
        }
        $casesWithReturnCount = $this->resolveReturnCount($switch);
        // has same amount of returns as switches
        return \count($switch->cases) === $casesWithReturnCount;
    }
    private function isTryCatchAlwaysReturn(TryCatch $tryCatch) : bool
    {
        if (!$this->hasStmtsAlwaysReturn($tryCatch->stmts)) {
            return \false;
        }
        foreach ($tryCatch->catches as $catch) {
            return $this->hasStmtsAlwaysReturn($catch->stmts);
        }
        return \true;
    }
    /**
     * @see https://phpstan.org/writing-php-code/phpdoc-types#bottom-type
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Expr\Closure|\PhpParser\Node\Stmt\Function_ $functionLike
     */
    private function hasNeverType($functionLike) : bool
    {
        return $this->betterNodeFinder->hasInstancesOf($functionLike, [Throw_::class]);
    }
    private function resolveReturnCount(Switch_ $switch) : int
    {
        $casesWithReturnCount = 0;
        foreach ($switch->cases as $case) {
            foreach ($case->stmts as $caseStmt) {
                if (!$caseStmt instanceof Return_) {
                    continue;
                }
                ++$casesWithReturnCount;
                break;
            }
        }
        return $casesWithReturnCount;
    }
}
