<?php

namespace App\Services;

use App\Services\InterfacesTest\Common;
use PhpBoot\Di\Attribute\Primary;
use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Attribute\Service;

#[Service(name: 'Service1Name')]
#[Primary]
class Service1
{

    public function __construct(
        private Common $commonService,
        #[Qualifier(name: 'commonImplNamed')] private Common $commonServiceNamed
    )
    {

    }
}