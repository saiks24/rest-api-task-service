#! /usr/bin/php
<?php
require_once __DIR__.'/vendor/autoload.php';

use Saiks24\Worker\SingleThreadWorker;
try {
    if(!extension_loaded('pcntl')) {
        echo 'Extension PCNTL dont install'.PHP_EOL;
        exit(-1);
    }
    $worker = new SingleThreadWorker();
    pcntl_async_signals(true);
    pcntl_signal(SIGTERM,[&$worker,'stop']);
    $worker->run(__DIR__);
} catch (Exception $e) {
    echo 'Worker exception: ' . $e->getMessage().PHP_EOL;
    exit(-1);
}
