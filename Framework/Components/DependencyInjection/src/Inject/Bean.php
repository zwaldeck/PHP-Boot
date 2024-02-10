<?php

namespace PhpBoot\Di\Inject;

use ReflectionClass;

readonly class Bean
{
    private string $attributeType;
    private string|null $injectionName;
    private bool $isPrimary;
    private object $service;
    /** @var string[]  */
    private array $possibleInjectionClassNames;

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

        $this->possibleInjectionClassNames = $this->initPossibleInjectionClassNames();
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

    /**
     * @return string[]
     */
    public function getPossibleInjectionClassNames(): array
    {
        return $this->possibleInjectionClassNames;
    }

    /**
     * @return string[]
     */
    private function initPossibleInjectionClassNames(): array
    {
        $class = new ReflectionClass($this->service);

        return array_merge(
            $class->getInterfaceNames(),
            $this->getAllParentClassNames($class)
        );
    }

    /**
     * @param string[] $classes
     * @param ReflectionClass $class
     * @return string[]
     */
    private function getAllParentClassNames(ReflectionClass $class, array $classes = []): array
    {
        $parent = $class->getParentClass();

        if ($parent !== false) {
            $classes = array_merge($classes, $this->getAllParentClassNames($class, $classes));
        }

        return $classes;
    }


}