<?php

namespace PhpBoot\Di\Scanner\Model;

use ReflectionParameter;

readonly class ConstructorInjectionArg
{
    private ReflectionParameter $parameter;
    private ConstructorInjectionType $type;
    private string|null $qualifier;

    /**
     * @param ReflectionParameter $parameter
     * @param ConstructorInjectionType $type
     * @param string|null $qualifier
     */
    public function __construct(ReflectionParameter $parameter, ConstructorInjectionType $type, string|null $qualifier)
    {
        $this->parameter = $parameter;
        $this->type = $type;
        $this->qualifier = $qualifier;
    }

    public function getParameter(): ReflectionParameter
    {
        return $this->parameter;
    }

    public function getType(): ConstructorInjectionType
    {
        return $this->type;
    }

    public function getQualifier(): ?string
    {
        return $this->qualifier;
    }

    public function hasQualifier(): bool
    {
        return $this->qualifier !== null;
    }

    public function getParameterClassName(): string
    {
        return $this->parameter->getType()->getName();
    }

    public function hasDefaultValue(): bool
    {
        return $this->parameter->isDefaultValueAvailable();
    }

    public function allowsNull(): bool
    {
        return $this->parameter->allowsNull() || $this->parameter->getType()->allowsNull();
    }


}