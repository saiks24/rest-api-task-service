<?php

namespace Saiks24\Storage;


use Saiks24\Command\CommandInterface;

class RedisTaskStorage implements StorageInterface
{
    /** @var \Redis */
    private $redisConnect;

    public function __construct(\Redis $redis)
    {
        $redis->pconnect('0.0.0.0');
        $this->redisConnect = $redis;
    }

    public function add(CommandInterface $command)
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
        return $command;
    }

    public function delete(string $id): bool
    {
        return (bool)$this->redisConnect->delete('tasks:'.$id);
    }

}