<?php

namespace PhpBoot\Di\Scanner\Model;

use ReflectionClass;

readonly class ServiceInjectionInfo
{
    private ReflectionClass $class;
    private string|null $injectionName;
    private string $serviceAttributeClassName;
    private bool $primary;
    /** @var ConstructorInjectionArg[]  */
    private array $constructorInjectionArgs;

    /**
     * @param ReflectionClass $class
     * @param string|null $injectionName
     * @param string $serviceAttributeClassName
     * @param bool $primary
     * @param ConstructorInjectionArg[] $constructorInjectionArgs
     */
    public function __construct(ReflectionClass $class, string|null $injectionName, string $serviceAttributeClassName, bool $primary, array $constructorInjectionArgs)
    {
        $this->class = $class;
        $this->injectionName = $injectionName;
        $this->serviceAttributeClassName = $serviceAttributeClassName;
        $this->primary = $primary;
        $this->constructorInjectionArgs = $constructorInjectionArgs;
    }

    public function getClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getInjectionName(): string|null
    {
        return $this->injectionName;
    }

    public function getServiceAttributeClassName(): string
    {
        return $this->serviceAttributeClassName;
    }

    public function isPrimary(): bool
    {
        return $this->primary;
    }

    public function getConstructorInjectionArgs(): array
    {
        return $this->constructorInjectionArgs;
    }
}