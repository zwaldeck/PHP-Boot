<?php

namespace App\Services\InterfacesTest;

use PhpBoot\Di\Attribute\Service;

#[Service]
class CommonImplWorld implements Common
{

    public function handle(): string
    {
        return "world";
    }
}