<?php

require_once __DIR__.'/vendor/autoload.php';

use \Saiks24\App\App;
use \Saiks24\App\Config;

$pathToConfig = __DIR__.'/config/config.php';
$application = App::make();
$application->setConfig(
    new Config($pathToConfig)
);
$application->run();
