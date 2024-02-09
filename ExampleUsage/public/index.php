<?php

use PhpBoot\Di\Scanner\ServiceScanner;

require dirname(__DIR__) . '/vendor/autoload.php';
$psr4Mappings = require dirname(__DIR__) . '/vendor/composer/autoload_psr4.php';

$scanner = new ServiceScanner(
    $psr4Mappings,
    [
        'App\\Services'
    ]
);

$scannedServices  = $scanner->scan();

foreach ($scannedServices as $scannedService) {
    var_dump($scannedService);
}
