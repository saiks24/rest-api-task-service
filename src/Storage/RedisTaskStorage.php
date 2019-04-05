<?php

namespace Saiks24\Storage;


use Saiks24\App\App;
use Saiks24\App\Config;
use Saiks24\Command\CommandInterface;
use Saiks24\Command\TestCommand;

class RedisTaskStorage implements StorageInterface
{
    /** @var \Redis */
    private $redisConnect;

    public function __construct(\Redis $redis)
    {
        $config = new Config(__DIR__.'/../../config/config.php');
        $redisConfig = $config->configGetValue('redis');
        $redis->pconnect($redisConfig['host']);
        $this->redisConnect = $redis;
    }

    public function add(CommandInterface $command) : void
    {
        $this->redisConnect->hMset(
          'tasks:'.$command->getId(),
          [
            'time'=>time(),'command'=>serialize($command),'status'=>$command->getStatus()
          ]
        );
    }

    public function get(string $id): CommandInterface
    {
        $command = unserialize(
          $this->redisConnect->hGet('tasks:'.$id,'command')
        );
        if($command instanceof CommandInterface) {
            return $command;
        }
        $command = new TestCommand(time(),'undefined',$id);
        return $command;
    }

    public function delete(string $id): bool
    {
        return (bool)$this->redisConnect->delete('tasks:'.$id);
    }

}