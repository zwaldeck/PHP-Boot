<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Exception\BeanCreationException;
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
     * @return Bean
     * @throws BeanCreationException
     */
    public static function createBean(ServiceInjectionInfo $scannedService, Map $beanMap): Bean
    {
        try {
            $constructorArgs = [];
            foreach ($scannedService->getConstructorInjectionArgs() as $arg) {
                $constructorArgs[] = self::resolveConstructorArg($arg, $beanMap, $scannedService->getClass()->getName());
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
     * @param string $serviceClassName
     * @return mixed
     * @throws BeanCreationException
     * @throws ReflectionException
     */
    private static function resolveConstructorArg(ConstructorInjectionArg $arg, Map $beanMap, string $serviceClassName): mixed
    {
        if ($arg->getType() === ConstructorInjectionType::PROPERTY) {
            // TODO: Implement property handling
            return "";
        }

        return self::resolveBean($arg, $beanMap, $serviceClassName);
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