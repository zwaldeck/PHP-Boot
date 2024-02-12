<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

use PhpBoot\Di\Inject\PropertyRegistry;
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
$propertyRegistry = new PropertyRegistry([
    "hello" => [
        "world" => "Hello world from property injection!"
    ]
]);

$serviceCreator = new ServiceCreator();
$beanMap = $serviceCreator->createServices($scannedServices, $propertyRegistry);

var_dump($beanMap);

foreach ($beanMap as $className => $bean) {
    echo "-----------------CLASS NAME-----------------";
    var_dump($className);
    echo "-----------------BEAN-----------------";
    var_dump($bean);
    echo "-----------------OBJECT-----------------";
    var_dump($bean->getService());
}
