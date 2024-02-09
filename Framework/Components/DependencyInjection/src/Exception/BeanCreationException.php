<?php

namespace PhpBoot\Di\Exception;

use Exception;

class BeanCreationException extends Exception
{
    #[\Override]
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}";
    }


}