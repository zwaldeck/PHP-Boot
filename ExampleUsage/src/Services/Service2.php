<?php

namespace App\Services;

use App\Model\Test;
use App\Services\SubFolder\SubFolderService1;
use PhpBoot\Di\Attribute\Property;
use PhpBoot\Di\Attribute\Qualifier;
use PhpBoot\Di\Attribute\Service;

#[Service]
class Service2
{
    public function __construct(
        #[Qualifier(name: 'Service1Name')] Service1 $service1,
        SubFolderService1 $subFolderService1,
        #[Property(name: 'hello.world')] string $helloWorld,
        Test|null $test1,
        ?Test $test2 = null,
        string $withDefault = 'withDefault'
    )
    {

    }
}