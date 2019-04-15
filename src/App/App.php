<?php
namespace Saiks24\App;

use Saiks24\Http\CommandController;
use Saiks24\Middleware\CheckCredentialMiddleware;
use Saiks24\Middleware\RateLimiter;
use Saiks24\Verification\FromConfigCredentialValidator;

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
            $credentialValidator = new CheckCredentialMiddleware();
            $credentialValidator->setVerify(new FromConfigCredentialValidator());
            $rateLimit = new RateLimiter(new \Redis(),10);
            $app->post(
              '/api/v1/command/create',CommandController::class.':create'
            )->add($credentialValidator)
             ->add($rateLimit);

            $app->delete(
              '/api/v1/command/delete',CommandController::class.':delete'
            )->add($credentialValidator)
             ->add($rateLimit);

            $app->get(
              '/api/v1/command/info',CommandController::class.':info'
            )->add($credentialValidator)
             ->add($rateLimit);

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