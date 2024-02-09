<?php

namespace PhpBoot\Di\Scanner;

use PhpBoot\Di\Exception\ServiceScannerException;
use ReflectionIntersectionType;
use ReflectionParameter;
use ReflectionUnionType;

final readonly class ServiceScannerValidator
{
    private function __construct()
    {
    }

    /**
     * @param ReflectionParameter $parameter
     * @param string $serviceClass
     * @return void
     * @throws ServiceScannerException
     */
    public static function validateConstructorParameterType(ReflectionParameter $parameter, string $serviceClass): void
    {
        $name = $parameter->getName();
        $type = $parameter->getType();

        if ($type == null) {
            throw new ServiceScannerException("The constructor parameter '{$name}' in class '{$serviceClass}' does not have a type. A type is required!");
        }

        if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            throw new ServiceScannerException("The constructor parameter '{$name}' in class '{$serviceClass}' has multiple types. This is not yet supported.");
        }
    }
}