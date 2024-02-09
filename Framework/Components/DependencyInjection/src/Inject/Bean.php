<?php

namespace PhpBoot\Di\Inject;

readonly class Bean
{
    private string $attributeType;
    private string|null $injectionName;
    private bool $isPrimary;
    private object $service;

    /**
     * @param string $attributeType
     * @param string|null $injectionName
     * @param bool $isPrimary
     * @param object $service
     */
    public function __construct(string|null $injectionName, bool $isPrimary, string $attributeType, object $service)
    {
        $this->attributeType = $attributeType;
        $this->injectionName = $injectionName;
        $this->isPrimary = $isPrimary;
        $this->service = $service;
    }

    public function getAttributeType(): string
    {
        return $this->attributeType;
    }

    public function getInjectionName(): string|null
    {
        return $this->injectionName;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getService(): object
    {
        return $this->service;
    }




}