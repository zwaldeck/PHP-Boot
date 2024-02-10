<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Service;

#[Service(name: 'commonImplNamed')]
class CommonImplNamed implements Common
{

    public function handle(): string
    {
        return "named";
    }
}