<?php

namespace App\Services\Circular;

use PhpBoot\Di\Attribute\Service;

//#[Service]
class Circular2
{
    public function __construct(
        private Circular1 $circular1
    )
    {

    }

}