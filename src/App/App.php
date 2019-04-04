<?php
namespace Saiks24\App;

use Saiks24\Http\CommandController;
use Saiks24\Middleware\CheckCredentialMiddleware;
use Saiks24\Middleware\RateLimiter;

class App
{
    /** @var \Saiks24\App\App */
    private static $instance;

    /** @var \Saiks24\App\Config */
    private $config;

    private function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Run application instance
     */
    public function run()
    {
        try {
            $app = new \Slim\App();

            $app->post(
              '/api/v1/command/create',CommandController::class.':create'
            )->add(
              new CheckCredentialMiddleware()
            )->add(new RateLimiter(new \Redis(),10));

            $app->delete(
              '/api/v1/command/delete',CommandController::class.':delete'
            )->add(
              new CheckCredentialMiddleware()
            )->add(new RateLimiter(new \Redis(),10));

            $app->get(
              '/api/v1/command/info',CommandController::class.':info'
            )->add(
              new CheckCredentialMiddleware()
            )->add(new RateLimiter(new \Redis(),10));

            $app->run();
        } catch (\Exception $e) {
            echo 'Bootstrap exception:'. $e->getMessage() . PHP_EOL;
            exit(-1);
        }
    }

    /** Make application instance
     * @param string $pathToConfig
     *
     * @return \Saiks24\App\App
     */
    public static function make(string $pathToConfig = '') : self
    {
        try {
            if(!empty(static::$instance)) {
                return static::$instance;
            }
            if(!is_file($pathToConfig)) {
                throw new \Exception('Wrong config file');
            }
            $config = new Config($pathToConfig);
            $app = new App($config);
            self::$instance = $app;
            return $app;
        } catch (\Exception $e) {
            echo 'Bootstrap exception: '. $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * @return \Saiks24\App\Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}