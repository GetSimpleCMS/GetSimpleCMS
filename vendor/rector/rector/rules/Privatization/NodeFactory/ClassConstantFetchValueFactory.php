<?php

declare (strict_types=1);
namespace Rector\Privatization\NodeFactory;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use Rector\Core\PhpParser\Node\NodeFactory;
use Rector\Core\PhpParser\Node\Value\ValueResolver;
use Rector\Privatization\Reflection\ClassConstantsResolver;
final class ClassConstantFetchValueFactory
{
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\Value\ValueResolver
     */
    private $valueResolver;
    /**
     * @readonly
     * @var \Rector\Core\PhpParser\Node\NodeFactory
     */
    private $nodeFactory;
    /**
     * @readonly
     * @var \Rector\Privatization\Reflection\ClassConstantsResolver
     */
    private $classConstantsResolver;
    public function __construct(ValueResolver $valueResolver, NodeFactory $nodeFactory, ClassConstantsResolver $classConstantsResolver)
    {
        $this->valueResolver = $valueResolver;
        $this->nodeFactory = $nodeFactory;
        $this->classConstantsResolver = $classConstantsResolver;
    }
    /**
     * @param class-string $classWithConstants
     */
    public function create(Expr $expr, string $classWithConstants, bool $caseInsensitive) : ?ClassConstFetch
    {
        $value = $this->valueResolver->getValue($expr);
        if ($value === null) {
            return null;
        }
        $constantNamesToValues = $this->classConstantsResolver->getClassConstantNamesToValues($classWithConstants);
        foreach ($constantNamesToValues as $constantName => $constantValue) {
            if ($caseInsensitive) {
                $constantValue = \strtolower((string) $constantValue);
                $value = \strtolower((string) $value);
            }
            if ($constantValue !== $value) {
                continue;
            }
            return $this->nodeFactory->createClassConstFetch($classWithConstants, $constantName);
        }
        return null;
    }
}
