<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Exception\BeanCreationException;
use PhpBoot\Di\Exception\PropertyNotFoundException;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionType;
use PhpBoot\Di\Scanner\Model\ServiceInjectionInfo;
use PhpBoot\Utils\Structure\Map;
use ReflectionException;

final readonly class ServiceFactory
{
    private function __construct()
    {

    }

    /**
     * @param ServiceInjectionInfo $scannedService
     * @param Map $beanMap
     * @param PropertyRegistry $propertyRegistry
     * @return Bean
     * @throws BeanCreationException
     */
    public static function createBean(ServiceInjectionInfo $scannedService, Map $beanMap, PropertyRegistry $propertyRegistry): Bean
    {
        try {
            $constructorArgs = [];
            foreach ($scannedService->getConstructorInjectionArgs() as $arg) {
                $constructorArgs[] = self::resolveConstructorArg($arg, $beanMap, $propertyRegistry, $scannedService->getClass()->getName());
            }

            $object = empty($constructorArgs) ?
                $scannedService->getClass()->newInstance() :
                $scannedService->getClass()->newInstance(...$constructorArgs);

            return new Bean($scannedService->getInjectionName(), $scannedService->isPrimary(), $scannedService->getServiceAttributeClassName(), $object);
        } catch (ReflectionException $e) {
            throw new BeanCreationException("Got a ReflectionException that is unrecoverable: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param ConstructorInjectionArg $arg
     * @param Map $beanMap
     * @param PropertyRegistry $propertyRegistry
     * @param string $serviceClassName
     * @return mixed
     * @throws BeanCreationException
     * @throws ReflectionException
     */
    private static function resolveConstructorArg(
        ConstructorInjectionArg $arg, Map $beanMap, PropertyRegistry $propertyRegistry, string $serviceClassName
    ): mixed
    {
        if ($arg->getType() === ConstructorInjectionType::PROPERTY) {
            return self::resolveProperty($arg, $propertyRegistry, $serviceClassName);
        }

        return self::resolveBean($arg, $beanMap, $serviceClassName);
    }

    /**
     * @param ConstructorInjectionArg $arg
     * @param PropertyRegistry $propertyRegistry
     * @param string $serviceClassName
     * @return string|int|bool|array
     * @throws BeanCreationException
     */
    private static function resolveProperty(
        ConstructorInjectionArg $arg, PropertyRegistry $propertyRegistry, string $serviceClassName
    ): string|int|bool|array
    {
        $propertyName = $arg->getPropertyName();
        try {
            return $propertyRegistry->getPropertyByName($propertyName);
        } catch (PropertyNotFoundException $e) {
            throw new BeanCreationException("Could not find property '{$propertyName}' for injection into '{$serviceClassName}'", 0, $e);
        }
    }

    /**
     * @param ConstructorInjectionArg $arg
     * @param Map $beanMap
     * @param string $serviceClassName
     * @return mixed
     * @throws BeanCreationException
     * @throws ReflectionException
     */
    private static function resolveBean(ConstructorInjectionArg $arg, Map $beanMap, string $serviceClassName): mixed
    {
        $possibleInjections = [];
        /**
         * @var string $beanClassName
         * @var Bean $bean
         */
        foreach ($beanMap->getAll() as $beanClassName => $bean) {
            if ($arg->hasQualifier() && $bean->getInjectionName() !== null && $bean->getInjectionName() === $arg->getQualifier()) {
                return $bean->getService();
            }

            if ($arg->getParameterClassName() === $beanClassName) {
                return $bean->getService();
            }

            if (in_array($arg->getParameterClassName(), $bean->getPossibleInjectionClassNames())) {
                $possibleInjections[] = $bean;
            }
        }

        if (!empty($possibleInjections)) {
            foreach ($possibleInjections as $possibleInjection) {
                if ($possibleInjection->isPrimary()) {
                    return $possibleInjection->getService();
                }
            }

            $possibleInjectionsCount = count($possibleInjections);
            throw new BeanCreationException("We found {$possibleInjectionsCount} possible injections for Bean with the type '{$arg->getParameterClassName()}' into '{$serviceClassName}'. You can specify a name or mark one of the services as a primary service.");
        }

        if ($arg->hasDefaultValue()) {
            return $arg->getParameter()->getDefaultValue();
        }

        if ($arg->allowsNull()) {
            return null;
        }


        if ($arg->hasQualifier()) {
            throw new BeanCreationException("Can not inject Bean with the name '{$arg->getQualifier()}' into '{$serviceClassName}'. Did you specify a service with this name?");
        } else {
            throw new BeanCreationException("Can not inject Bean with the type '{$arg->getParameterClassName()}' into '{$serviceClassName}'. Did you specify a service attribute on this type?");
        }
    }


}