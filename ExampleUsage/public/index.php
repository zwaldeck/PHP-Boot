<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpBoot\Di\Inject\ServiceCreator;
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

$serviceCreator = new ServiceCreator();
$beanMap = $serviceCreator->createServices($scannedServices);

var_dump($beanMap);

foreach ($beanMap as $className => $bean) {
    echo "-----------------BEAN-----------------";
    var_dump($bean);
    echo "-----------------CLASS NAME-----------------";
    var_dump($className);
    echo "-----------------OBJECT-----------------";
    var_dump($bean->getService());
}
