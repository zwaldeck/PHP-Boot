<?php

namespace PhpBoot\Di\Scanner;

use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Attribute\Property;
use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Attribute\Service;
use PhpBoot\Di\Exception\ServiceScannerException;
use PhpBoot\Di\Scanner\Model\Builder\ConstructorInjectionArgBuilder;
use PhpBoot\Di\Scanner\Model\Builder\ServiceInjectionInfoBuilder;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionType;
use PhpBoot\Di\Scanner\Model\ServiceInjectionInfo;
use PhpBoot\Utils\ArrayUtils;
use PhpBoot\Utils\StringUtils;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionParameter;

class ServiceScanner
{
    private array $psr4Mappings;

    /** @var string[] */
    private array $baseNamespaces;

    /**
     * @param array $psr4Mappings
     * @param string[] $baseNamespaces
     */
    public function __construct(array $psr4Mappings, array $baseNamespaces)
    {
        $this->psr4Mappings = $psr4Mappings;
        $this->baseNamespaces = $baseNamespaces;
    }

    /**
     * @return ServiceInjectionInfo[]
     * @throws ServiceScannerException
     */
    public function scan(): array
    {
        $scannedClasses = [];
        foreach ($this->baseNamespaces as $namespace) {
            $scannedClasses = array_merge($scannedClasses, $this->scanNamespace($namespace));
        }

        $serviceClasses = [];
        foreach ($scannedClasses as $scannedClass) {
            $serviceAttributes = $scannedClass->getAttributes(Service::class, ReflectionAttribute::IS_INSTANCEOF);
            $serviceAttributesCount = count($serviceAttributes);

            if ($serviceAttributesCount === 0) continue;

            if ($serviceAttributesCount > 1) {
                throw new ServiceScannerException("The service with class '{$scannedClass->getName()}' has multiple Service attributes. A Service can only have 1");
            }

            $serviceClasses[] = $this->buildServiceInjectionInfo($scannedClass, ArrayUtils::getFirstElement($serviceAttributes));
        }

        return $serviceClasses;
    }

    /**
     * @param string $namespace
     * @return ReflectionClass[]
     * @throws ServiceScannerException
     */
    private function scanNamespace(string $namespace): array
    {
        $scannedClasses = [];

        foreach ($this->findStartingDirectories($namespace) as $directory) {
            $scannedClasses = array_merge($scannedClasses, FileScanner::scanForClassesInNamespace($namespace, $directory, true));
        }

        return $scannedClasses;
    }

    /**
     * @param string $namespace
     * @return string[]
     * @throws ServiceScannerException
     */
    private function findStartingDirectories(string $namespace): array
    {
        $namespace = StringUtils::addTrailingString($namespace, '\\');
        if (array_key_exists($namespace, $this->psr4Mappings)) {
            return $this->psr4Mappings[$namespace];
        }

        foreach ($this->psr4Mappings as $key => $directories) {
            if (!str_starts_with($namespace, $key)) continue;

            $pathToAppend = StringUtils::addBeginningString(ltrim(rtrim($namespace, '\\'), $key), DIRECTORY_SEPARATOR);

            return array_map(static fn($dir) => $dir . $pathToAppend, $directories);
        }

        throw new ServiceScannerException("Could not find any PSR-4 mapping for namespace '{$namespace}'. Was there a typo?");
    }

    /**
     * @param ReflectionClass $class
     * @param ReflectionAttribute $serviceAttribute
     * @return ServiceInjectionInfo
     * @throws ServiceScannerException
     */
    private function buildServiceInjectionInfo(ReflectionClass $class, ReflectionAttribute $serviceAttribute): ServiceInjectionInfo
    {
        $builder = new ServiceInjectionInfoBuilder();
        return $builder
            ->withClass($class)
            ->withInjectionName($this->getInjectionName($serviceAttribute))
            ->withServiceAttributeClassName($serviceAttribute->getName())
            ->withPrimary($this->hasPrimaryAttribute($class))
            ->withConstructorArgs($this->getConstructorInjectionArgs($class))
            ->build();
    }

    private function getInjectionName(ReflectionAttribute $serviceAttribute): string|null
    {
        /** @var Service $service */
        $service = $serviceAttribute->newInstance();

        if (StringUtils::isBlank($service->name)) {
            return null;
        }

        return $service->name;
    }

    private function hasPrimaryAttribute(ReflectionClass $class): bool
    {
        $attributes = $class->getAttributes(Primary::class, ReflectionAttribute::IS_INSTANCEOF);

        return count($attributes) > 0;
    }

    /**
     * @param ReflectionClass $class
     * @return ConstructorInjectionArg[]
     * @throws ServiceScannerException
     */
    private function getConstructorInjectionArgs(ReflectionClass $class): array
    {
        $constructor = $class->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            ServiceScannerValidator::validateConstructorParameterType($parameter, $class->getName());
            $args[] = $this->buildConstructorInjectionArg($parameter);
        }
        return $args;
    }

    private function buildConstructorInjectionArg(ReflectionParameter $parameter): ConstructorInjectionArg
    {
        $builder = new ConstructorInjectionArgBuilder();
        return $builder
            ->withParameter($parameter)
            ->withType($this->getConstructorInjectionType($parameter))
            ->withQualifier($this->getConstructorInjectionQualifier($parameter))
            ->build();
    }

    private function getConstructorInjectionType(ReflectionParameter $parameter): ConstructorInjectionType
    {
        $attributes = $parameter->getAttributes(Property::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) {
            return ConstructorInjectionType::BEAN;
        }

        return ConstructorInjectionType::PROPERTY;
    }

    private function getConstructorInjectionQualifier(ReflectionParameter $parameter): string|null
    {
        $attributes = $parameter->getAttributes(Qualifier::class, ReflectionAttribute::IS_INSTANCEOF);

        if (empty($attributes)) {
            return null;
        }

        return ArrayUtils::getFirstElement($attributes)->newInstance()->name;
    }
}