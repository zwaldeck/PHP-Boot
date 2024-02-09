<?php

namespace PhpBoot\Di\Inject;

use PhpBoot\Di\Exception\BeanCreationException;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionArg;
use PhpBoot\Di\Scanner\Model\ConstructorInjectionType;
use PhpBoot\Di\Scanner\Model\ServiceInjectionInfo;
use PhpBoot\Utils\Structure\Map;
use ReflectionNamedType;

class ServiceCreator
{
    public function createServices(array $scannedServices): Map
    {
        $beanMap = new Map();
        $cachedDependencies = [];

        $this->createBeanMap($scannedServices, $beanMap, $cachedDependencies);

        return $beanMap;
    }

    /**
     * @param ServiceInjectionInfo[] $scannedServices
     * @param Map<string, Bean> $beanMap
     * @param array<string, string> $cachedDependencies
     * @return void
     * @throws BeanCreationException
     */
    private function createBeanMap(array $scannedServices, Map &$beanMap, array &$cachedDependencies): void
    {
        foreach ($scannedServices as $key => $scannedService) {
            $dependencies = $this->getDependencies($scannedService, $cachedDependencies, $scannedServices);

            if ($this->areAllDependenciesResolved($beanMap->getAllKeys(), $dependencies)) {
                $createdBean = ServiceFactory::createBean($scannedService, $beanMap);
                $beanMap->add($scannedService->getClass()->getName(), $createdBean);
                unset($scannedServices[$key]);
            }
        }

        if (!empty($scannedServices)) {
            // TODO: On 255 times in this function, quit with an exception of circular dependencies

            $this->createBeanMap($scannedServices, $beanMap, $cachedDependencies);
        }
    }

    /**
     * @param ServiceInjectionInfo $scannedService
     * @param array<string, string> $cachedDependencies
     * @param ServiceInjectionInfo[] $scannedServices
     * @return string[]
     */
    private function getDependencies(ServiceInjectionInfo $scannedService, array &$cachedDependencies, array $scannedServices): array
    {
        if (array_key_exists($scannedService->getClass()->getName(), $cachedDependencies)) {
            return $cachedDependencies[$scannedService->getClass()->getName()];
        }

        $dependencies = [];
        foreach ($scannedService->getConstructorInjectionArgs() as $constructorArg) {
            if ($constructorArg->getType() === ConstructorInjectionType::PROPERTY) continue;

            if ($this->scannedServicesContainsService($constructorArg, $scannedServices)) {
                $dependencies[] = $constructorArg->getParameter()->getType()->getName();
            }
        }

        $cachedDependencies[$scannedService->getClass()->getName()] = $dependencies;
        return $dependencies;
    }

    /**
     * @param ConstructorInjectionArg $arg
     * @param ServiceInjectionInfo[] $scannedServices
     * @return bool
     */
    private function scannedServicesContainsService(ConstructorInjectionArg $arg, array $scannedServices): bool
    {
        $type = $arg->getParameter()->getType();

        if (!($type instanceof ReflectionNamedType)) {
            return false;
        }

        foreach ($scannedServices as $scannedService) {
            if ($arg->hasQualifier() && $arg->getQualifier() === $scannedService->getInjectionName()) {
                return true;
            }

            if ($type->getName() === $scannedService->getClass()->getName()) {
                return true;
            }
        }

        return false;
    }

    private function areAllDependenciesResolved(array $beanClassNames, array $dependencies): bool
    {
        foreach ($dependencies as $dependency) {
            $found = false;
            foreach ($beanClassNames as $class) {
                if ($class === $dependency) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        return true;
    }
}