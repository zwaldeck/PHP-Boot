<?php

namespace PhpBoot\Di\Scanner\Model\Builder;

use PhpBoot\Di\Scanner\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionType;
use ReflectionParameter;

class ConstructorInjectionArgBuilder
{
    private ReflectionParameter $parameter;
    private ConstructorInjectionType $type;
    private string|null $qualifier = null;

    public function withParameter(ReflectionParameter $parameter): self
    {
        $this->parameter = $parameter;
        return $this;
    }

    public function withType(ConstructorInjectionType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withQualifier(string|null $qualifier): self
    {
        $this->qualifier = $qualifier;
        return $this;
    }

    public function build(): ConstructorInjectionArg
    {
        return new ConstructorInjectionArg(
            $this->parameter,
            $this->type,
            $this->qualifier
        );
    }
}