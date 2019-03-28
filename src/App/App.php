<?php
namespace Saiks24\App;

use Saiks24\Http\CommandController;

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

    /** Make application instance
     * @param array $pathToConfigFile
     *
     * @return \Saiks24\App\App
     */
    public static function make(array $pathToConfigFile)
    {
        try {
            if(!empty(static::$instance)) {
                return static::$instance;
            }
            $config = [];
            foreach ($pathToConfigFile as $configPath) {
                if(!is_file($configPath)) {
                    throw new \Exception('Path to config file is wrong');
                }
                $configContent = include ($configPath);
                if(!empty($configContent)) {
                    array_merge($config,$configContent);
                }
            }
            $app = new App($config);
            self::$instance = $app;
            return $app;
        } catch (\Exception $e) {
            echo 'Bootstrap exception:'. $e->getMessage() . PHP_EOL;
            exit(-1);
        }
    }

    /**
     * Run application instance
     */
    public function run()
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

    /** Get param from config by name
     * @param string $param
     *
     * @return mixed|null
     */
    public function configGetValue(\string $param)
    {
        return $this->config[$param] ?? null;
    }
}