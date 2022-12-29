<?php

declare (strict_types=1);
namespace Rector\Core\Contract\PHPStan\Reflection\TypeToCallReflectionResolver;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
/**
 * @template TType as Type
 */
interface TypeToCallReflectionResolverInterface
{
    public function supports(Type $type) : bool;
    /**
     * @param TType $type
     * @return FunctionReflection|MethodReflection|null
     */
    public function resolve(Type $type, Scope $scope);
}
