<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Exception\PropertyNotFoundException;

readonly class PropertyRegistry
{
    private const string NAME_SEPARATOR = '.';

    private array $properties;

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getPropertyByName(string $name): string|int|bool|array
    {
        $keys = explode(self::NAME_SEPARATOR, $name);

        $currentValue = $this->properties;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $currentValue)) {
                throw new PropertyNotFoundException("No property found with name: {$name}");
            }

            $currentValue = $currentValue[$key];
        }

        return $currentValue;
    }

    public function hasProperty(string $name): bool
    {
        try {
            $this->getPropertyByName($name);
            return true;
        } catch (PropertyNotFoundException) {
            return false;
        }
    }


}