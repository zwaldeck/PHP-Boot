<?php

namespace App\Services\Circular;

use PhpBoot\Di\Attribute\Service;

//#[Service]
class Circular1
{

    public function __construct(
        private Circular2 $circular2
    )
    {

    }
}