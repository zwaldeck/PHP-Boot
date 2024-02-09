<?php

namespace PhpBoot\Di\Scanner\Model\Builder;

use PhpBoot\Di\Scanner\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scanner\Model\ServiceInjectionInfo;
use ReflectionClass;

class ServiceInjectionInfoBuilder
{
    private ReflectionClass $class;
    private string $injectionName;
    private string $serviceAttributeClassName;
    private bool $primary = false;
    /** @var ConstructorInjectionArg[]  */
    private array $constructorArgs = [];

    public function withClass(ReflectionClass $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function withInjectionName(string $injectionName): self
    {
        $this->injectionName = $injectionName;
        return $this;
    }

    public function withServiceAttributeClassName(string $serviceAttributeClassName): self
    {
        $this->serviceAttributeClassName = $serviceAttributeClassName;
        return $this;
    }

    public function withPrimary(bool $primary): self
    {
        $this->primary = $primary;
        return $this;
    }

    /**
     * @param ConstructorInjectionArg[] $constructorArgs
     * @return $this
     */
    public function withConstructorArgs(array $constructorArgs): self
    {
        $this->constructorArgs = $constructorArgs;
        return $this;
    }

    public function build(): ServiceInjectionInfo
    {
        return new ServiceInjectionInfo(
            $this->class,
            $this->injectionName,
            $this->serviceAttributeClassName,
            $this->primary,
            $this->constructorArgs
        );
    }
}