<?php

declare (strict_types=1);
namespace Rector\Php80\NodeResolver;

use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\ParametersAcceptor;
final class RequireOptionalParamResolver
{
    /**
     * @return ParameterReflection[]
     */
    public function resolveFromParametersAcceptor(ParametersAcceptor $parametersAcceptor) : array
    {
        $optionalParams = [];
        $requireParams = [];
        foreach ($parametersAcceptor->getParameters() as $position => $parameterReflection) {
            if ($parameterReflection->getDefaultValue() === null && !$parameterReflection->isVariadic()) {
                $requireParams[$position] = $parameterReflection;
            } else {
                $optionalParams[$position] = $parameterReflection;
            }
        }
        return $requireParams + $optionalParams;
    }
}
