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
        #[Qualifier(name: 'Service1Name')] private Service1 $service1,
        private SubFolderService1 $subFolderService1,
        #[Property(name: 'hello.world')] private  string $helloWorld,
        private Test|null $test1,
        private ?Test $test2 = null,
        private string $withDefault = 'withDefault'
    )
    {

    }
}