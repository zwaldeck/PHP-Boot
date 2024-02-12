<?php

namespace PhpBoot\Di\Exception;

use Exception;

class PropertyNotFoundException extends Exception
{
    #[\Override]
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }


}