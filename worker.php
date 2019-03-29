<?php
require_once __DIR__.'/vendor/autoload.php';
try {
    $worker = new \Saiks24\Worker\SingleThreadWorker();
    $worker->run();
} catch (Exception $e) {
    echo 'Worker exception: ' . $e->getMessage().PHP_EOL;
    exit(-1);
}
