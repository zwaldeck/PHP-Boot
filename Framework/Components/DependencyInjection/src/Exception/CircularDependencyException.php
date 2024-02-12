<?php

namespace PhpBoot\Di\Exception;

use Exception;
use PhpBoot\Di\Scanner\Model\ServiceInjectionInfo;

class CircularDependencyException extends Exception
{
    /**
     * @var ServiceInjectionInfo[]
     */
    private array $scannedServices;

    /**
     * @param ServiceInjectionInfo[] $scannedServices
     */
    public function __construct(string $message, array $scannedServices, int $code = 0)
    {
        parent::__construct($message, $code);
        $this->scannedServices = $scannedServices;
    }

    #[\Override]
    public function __toString(): string
    {
        $servicesNotCreated = "[\n";

        foreach ($this->scannedServices as $scannedService) {
            $servicesNotCreated .= "\t {$scannedService->getClass()->getName()},\n";
        }
        $servicesNotCreated .= "]\n";

        return __CLASS__ . ": [{$this->code}]: {$this->message} --> beans that are not created: \n{$servicesNotCreated}";
    }


}