<?php
require_once __DIR__.'/vendor/autoload.php';
try {
    $worker = new \Saiks24\Worker\SingleThreadWorker();
    pcntl_async_signals(true);
    pcntl_signal(SIGTERM,[&$worker,'stop']);
    $worker->run(__DIR__);
} catch (Exception $e) {
    echo 'Worker exception: ' . $e->getMessage().PHP_EOL;
    exit(-1);
}
