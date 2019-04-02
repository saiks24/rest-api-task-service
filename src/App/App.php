<?php
namespace Saiks24\App;

use Saiks24\Http\CommandController;
use Saiks24\Middleware\CheckCredentialMiddleware;
use Saiks24\Middleware\RateLimiter;

class App
{
    /** @var \Saiks24\App\App */
    private static $instance;

    /** @var array */
    private $config;

    private function __construct(array $config)
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
            $configContent = self::getConfigContent($pathToConfig);
            $app = new App($configContent);
            self::$instance = $app;
            return $app;
        } catch (\Exception $e) {
            echo 'Bootstrap exception: '. $e->getMessage() . PHP_EOL;
        }
    }

    /** Return array with all config content
     * @param string $pathToConfig
     *
     * @return mixed
     */
    private static function getConfigContent(string $pathToConfig)
    {
        $configContent = include($pathToConfig);
        return $configContent;
    }

    /** Get param from config by name
     * @param string $param
     *
     * @return mixed|null
     */
    public function configGetValue(string $param)
    {
        return $this->config[$param] ?? null;
    }

}