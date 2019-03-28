<?php
require_once __DIR__.'/vendor/autoload.php';
use \Saiks24\App\App;

$pathToConfig = __DIR__.'/config/config.php';
$application = App::make($pathToConfig);
$application->run();