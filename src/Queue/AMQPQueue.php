<?php
namespace Saiks24\Queue;

use Saiks24\Command\CommandInterface;

class AMQPQueue
{

    public function addTaskToQueue(CommandInterface $command)
    {
        $connection = $this->connect();
        $channel = new \AMQPChannel($connection);
        $exchange = $this->instanceExchange($channel);
        $this->instanceQueue($channel);
        $exchange->publish(
          serialize($command),
          'task.queue',
          AMQP_NOPARAM,
          [
            'delivery_mode' => 2
          ]
        );
    }

    private function instanceQueue(\AMQPChannel $channel)
    {
        $queue = new \AMQPQueue($channel);
        $queue->setFlags(AMQP_DURABLE);
        $queue->setName('task_queue');
        $queue->declareQueue();
        $queue->bind('task_exchange','task.queue');
        return $queue;
    }

    private function instanceExchange(\AMQPChannel $channel)
    {
        $exchange = new \AMQPExchange($channel);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);
        $exchange->setName('task_exchange');
        $exchange->declareExchange();
        return $exchange;
    }

    private function connect() : \AMQPConnection
    {
        $cnn = new \AMQPConnection([
          'host'  => '0.0.0.0',
          'port'  => 5672,
          'vhost' => '/',
          'login' => 'guest',
          'password' => 'guest'
        ]);
        $cnn->pconnect();
        return $cnn;
    }
}