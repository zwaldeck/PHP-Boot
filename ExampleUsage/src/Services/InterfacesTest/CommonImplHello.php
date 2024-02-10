<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Attribute\Service;

#[Service]
#[Primary]
class CommonImplHello implements Common
{

    public function handle(): string
    {
        return "hello";
    }
}