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

    const DEFAULT_PATH_TO_CONFIG = __DIR__.'/../../config/config.php';

    private function __construct()
    {
        if(empty($this->config)) {
            $this->setConfig(
                new Config(self::DEFAULT_PATH_TO_CONFIG)
            );
        }
    }

    /**
     * Run application instance
     */
    public function run() : void
    {
        try {
            if(empty($this->config)) {
                throw new \InvalidArgumentException('Config file not set');
            }
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
     *
     * @return self
     */
    public static function make() : self
    {
        try {
            if(!empty(static::$instance)) {
                return static::$instance;
            }
            $app = new App();
            self::$instance = $app;
            return $app;
        } catch (\Exception $e) {
            echo 'Bootstrap exception: '. $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }
}