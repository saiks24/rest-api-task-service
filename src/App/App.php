<?php
namespace Saiks24\App;

use Saiks24\Http\CommandController;

class App
{
    private static $instance;

    private function __construct()
    {
        $this->bootstrap();
    }

    public static function make()
    {
        if(!empty(static::$instance)) {
            return static::$instance;
        }
        $app = new App();
        self::$instance = $app;
        return $app;
    }

    private function bootstrap()
    {
        try {
            $app = new \Slim\App();
            $app->post('/api/v1/command/create',CommandController::class.':create');
            $app->delete('/api/v1/command/delete',CommandController::class.':delete');
            $app->get('/api/v1/command/info',CommandController::class.':info');
            $app->run();
        } catch (\Exception $e) {
            echo 'Bootstrap exception:'. $e->getMessage() . PHP_EOL;
            exit(-1);
        }
    }
}